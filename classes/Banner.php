<?php
class Banner {
    private $db;

    public function __construct() { $this->db = Database::getInstance(); }

    private function positions() { return ['hero', 'mid', 'bottom']; }

    public function getAll($position = '', $status = '') {
        $where = "1=1"; $params = [];
        if ($position) { $where .= " AND position = ?"; $params[] = $position; }
        if ($status)   { $where .= " AND status = ?";   $params[] = $status; }
        return $this->db->fetchAll(
            "SELECT * FROM banners WHERE {$where} ORDER BY FIELD(position,'hero','mid','bottom'), sort_order ASC, id ASC",
            $params
        );
    }

    public function getById($id) {
        return $this->db->fetch("SELECT * FROM banners WHERE id=?", [$id]);
    }

    public function create($data, $file = null) {
        $image = ($file && !empty($file['name'])) ? $this->uploadImage($file) : null;
        $id = $this->db->insert('banners', [
            'title'      => sanitize($data['title'] ?? ''),
            'subtitle'   => sanitize($data['subtitle'] ?? ''),
            'image'      => $image,
            'link'       => sanitize($data['link'] ?? ''),
            'position'   => in_array($data['position'] ?? '', $this->positions(), true) ? $data['position'] : 'hero',
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'status'     => in_array($data['status'] ?? '', ['active', 'inactive'], true) ? $data['status'] : 'active',
        ]);
        logActivity('banner_created', 'banners', $id, null, $data);
        return $id;
    }

    public function update($id, $data, $file = null) {
        $old = $this->getById($id);
        if (!$old) return false;

        $fields = [
            'title'      => sanitize($data['title'] ?? ''),
            'subtitle'   => sanitize($data['subtitle'] ?? ''),
            'link'       => sanitize($data['link'] ?? ''),
            'position'   => in_array($data['position'] ?? '', $this->positions(), true) ? $data['position'] : 'hero',
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'status'     => in_array($data['status'] ?? '', ['active', 'inactive'], true) ? $data['status'] : 'active',
        ];

        if ($file && !empty($file['name'])) {
            $image = $this->uploadImage($file);
            if ($image) {
                $this->deleteImageFile($old['image']);
                $fields['image'] = $image;
            }
        }

        $this->db->update('banners', $fields, 'id=?', [$id]);
        logActivity('banner_updated', 'banners', $id, $old, $data);
        return true;
    }

    public function delete($id) {
        $old = $this->getById($id);
        if (!$old) return false;
        $this->deleteImageFile($old['image']);
        $this->db->delete('banners', 'id=?', [$id]);
        logActivity('banner_deleted', 'banners', $id, $old);
        return true;
    }

    private function uploadImage($file) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) return null;

        $dir = UPLOAD_PATH . '/banners/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $filename = "banner_" . time() . "_" . bin2hex(random_bytes(3)) . ".{$ext}";
        $dest = $dir . $filename;

        if (is_uploaded_file($file['tmp_name'])) {
            $ok = move_uploaded_file($file['tmp_name'], $dest);
        } else {
            $ok = rename($file['tmp_name'], $dest);
            if (!$ok) $ok = copy($file['tmp_name'], $dest) && unlink($file['tmp_name']);
        }
        if (!$ok) return null;
        @chmod($dest, 0644);
        return "uploads/banners/{$filename}";
    }

    private function deleteImageFile($path) {
        if (!$path || strpos($path, 'uploads/') !== 0) return;
        $full = APP_ROOT . '/' . $path;
        if (is_file($full)) @unlink($full);
    }
}
