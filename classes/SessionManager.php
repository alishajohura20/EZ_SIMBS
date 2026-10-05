<?php
class SessionManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new login session row. Each login creates its own row so a
     * user can be signed in on multiple devices simultaneously.
     */
    public function createSession($userId, $remember = false) {
        $token = bin2hex(random_bytes(32));
        $rememberToken = $remember ? bin2hex(random_bytes(32)) : null;
        $expires = date('Y-m-d H:i:s', time() + ($remember ? SESSION_REMEMBER_LIFETIME : SESSION_LIFETIME));

        $info = $this->parseUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '');

        $id = $this->db->insert('user_sessions', [
            'user_id' => (int)$userId,
            'token' => $token,
            'remember_token' => $rememberToken,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'device' => $info['device'],
            'last_activity' => date('Y-m-d H:i:s'),
            'expires_at' => $expires,
        ]);

        return [
            'id' => $id,
            'token' => $token,
            'remember_token' => $rememberToken,
            'expires_at' => $expires,
        ];
    }

    /**
     * Check a token is a live, un-expired session owned by the user.
     * On success, refreshes last_activity.
     */
    public function validateSession($userId, $token) {
        if (!$userId || !$token) return false;

        $session = $this->db->fetch(
            "SELECT * FROM user_sessions WHERE user_id = ? AND token = ?",
            [$userId, $token]
        );
        if (!$session) return false;

        if (!empty($session['expires_at']) && strtotime($session['expires_at']) < time()) {
            $this->db->delete('user_sessions', 'id = ?', [$session['id']]);
            return false;
        }

        $this->touch($session['id']);
        return true;
    }

    public function touch($id) {
        $this->db->update('user_sessions', ['last_activity' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
    }

    public function getById($id) {
        return $this->db->fetch("SELECT * FROM user_sessions WHERE id = ?", [$id]);
    }

    /** All live sessions for one user (newest activity first). */
    public function getActiveSessions($userId) {
        $this->cleanupExpired();
        return $this->db->fetchAll(
            "SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_activity DESC",
            [$userId]
        );
    }

    /** All live sessions across all users (admin view). */
    public function getAllSessions($filters = []) {
        $this->cleanupExpired();
        $sql = "SELECT s.*, u.name AS user_name, u.email, u.branch_id, r.name AS role_name
                FROM user_sessions s
                JOIN users u ON u.id = s.user_id
                JOIN roles r ON r.id = u.role_id";
        $params = [];
        $where = [];

        if (!empty($filters['search'])) {
            $where[] = "(u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (isset($filters['role']) && $filters['role'] !== '') {
            $where[] = "r.name = ?";
            $params[] = $filters['role'];
        }
        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY s.last_activity DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function countActiveForUser($userId) {
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS total FROM user_sessions WHERE user_id = ?",
            [$userId]
        );
        return (int)($row['total'] ?? 0);
    }

    /** Delete a single session. $userId scopes to the owner when given. */
    public function terminate($id, $userId = null) {
        if ($userId === null) {
            $this->db->delete('user_sessions', 'id = ?', [$id]);
        } else {
            $this->db->delete('user_sessions', 'id = ? AND user_id = ?', [$id, $userId]);
        }
    }

    public function terminateByToken($token) {
        if ($token) {
            $this->db->delete('user_sessions', 'token = ?', [$token]);
        }
    }

    /** Terminate every session of a user except the given token (used on password change, "end all other sessions"). */
    public function terminateOthers($token, $userId) {
        $this->db->delete('user_sessions', 'user_id = ? AND token <> ?', [$userId, $token]);
    }

    /** Terminate every session of a user (used on password reset / account lock). */
    public function terminateAllForUser($userId) {
        $this->db->delete('user_sessions', 'user_id = ?', [$userId]);
    }

    public function cleanupExpired() {
        $this->db->query("DELETE FROM user_sessions WHERE expires_at IS NOT NULL AND expires_at < NOW()");
    }

    /**
     * Restore a login from a remember-me cookie. Returns true and populates
     * $_SESSION when a live session row matches the cookie.
     */
    public function restoreRememberedSession($rememberToken) {
        if (!$rememberToken) return false;
        $this->cleanupExpired();

        $s = $this->db->fetch(
            "SELECT s.*, u.name, u.email, u.branch_id, r.name AS role_name, r.id AS role_id
             FROM user_sessions s
             JOIN users u ON u.id = s.user_id
             JOIN roles r ON r.id = u.role_id
             WHERE s.remember_token = ?",
            [$rememberToken]
        );
        if (!$s) {
            setcookie('remember_token', '', time() - 3600, '/');
            return false;
        }
        if (!empty($s['expires_at']) && strtotime($s['expires_at']) < time()) {
            $this->db->delete('user_sessions', 'id = ?', [$s['id']]);
            setcookie('remember_token', '', time() - 3600, '/');
            return false;
        }

        $_SESSION['user_id'] = (int)$s['user_id'];
        $_SESSION['user_name'] = $s['name'];
        $_SESSION['user_email'] = $s['email'];
        $_SESSION['user_role'] = $s['role_name'];
        $_SESSION['user_role_id'] = (int)$s['role_id'];
        $_SESSION['user_branch'] = $s['branch_id'];
        $_SESSION['session_token'] = $s['token'];

        $this->touch($s['id']);
        return true;
    }

    /** Human-readable device label stored on each session. */
    private function parseUserAgent($ua) {
        $device = 'Desktop';
        if (preg_match('/iPhone|Android.*Mobile|Windows Phone|Opera Mini|Mobile/i', $ua)) $device = 'Mobile';
        elseif (preg_match('/iPad|Tablet|Kindle|Silk/i', $ua)) $device = 'Tablet';

        $browser = 'Unknown';
        if (stripos($ua, 'Edg/') !== false) $browser = 'Edge';
        elseif (stripos($ua, 'OPR/') !== false || stripos($ua, 'Opera') !== false) $browser = 'Opera';
        elseif (stripos($ua, 'Chrome/') !== false) $browser = 'Chrome';
        elseif (stripos($ua, 'Firefox/') !== false) $browser = 'Firefox';
        elseif (stripos($ua, 'Safari/') !== false) $browser = 'Safari';
        elseif (stripos($ua, 'MSIE') !== false || stripos($ua, 'Trident') !== false) $browser = 'IE';

        $os = 'Unknown';
        if (preg_match('/Windows NT 10/', $ua)) $os = 'Windows';
        elseif (preg_match('/Windows NT 6\./', $ua)) $os = 'Windows';
        elseif (stripos($ua, 'Mac OS X') !== false) $os = 'macOS';
        elseif (stripos($ua, 'Android') !== false) $os = 'Android';
        elseif (preg_match('/iPhone|iPad|iPod/', $ua)) $os = 'iOS';
        elseif (stripos($ua, 'Linux') !== false) $os = 'Linux';

        return [
            'device' => trim("{$browser} \u{00b7} {$os} \u{00b7} {$device}"),
        ];
    }
}