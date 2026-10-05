<?php
class Settings {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function get($key, $default = '') {
        $result = $this->db->fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $result ? $result['setting_value'] : $default;
    }

    public function set($key, $value, $group = 'general') {
        $existing = $this->db->fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        if ($existing) {
            $this->db->update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
        } else {
            $this->db->insert('settings', ['setting_key' => $key, 'setting_value' => $value, 'setting_group' => $group]);
        }
    }

    public function getAll($group = '') {
        $where = $group ? "WHERE setting_group = ?" : "";
        $params = $group ? [$group] : [];
        return $this->db->fetchAll("SELECT * FROM settings {$where} ORDER BY setting_group, setting_key", $params);
    }

    public function getGrouped() {
        $settings = $this->db->fetchAll("SELECT * FROM settings ORDER BY setting_group, setting_key");
        $grouped = [];
        foreach ($settings as $s) {
            $grouped[$s['setting_group']][$s['setting_key']] = $s['setting_value'];
        }
        return $grouped;
    }

    public function saveBulk($data, $group = 'general') {
        foreach ($data as $key => $value) {
            $this->set($key, $value, $group);
        }
    }

    public function backup() {
        $backupDir = BACKUP_PATH;
        if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);

        $filename = 'ez_simbs_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $command = "mysqldump -u " . DB_USER . " -p" . DB_PASS . " " . DB_NAME . " > " . escapeshellarg($filepath) . " 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            logActivity('database_backup', 'settings', null, null, ['file' => $filename]);
            return ['success' => true, 'file' => $filename, 'path' => $filepath];
        }
        return ['success' => false, 'message' => 'Backup failed: ' . implode("\n", $output)];
    }

    public function restore($filepath) {
        if (!file_exists($filepath)) return ['success' => false, 'message' => 'Backup file not found'];

        $command = "mysql -u " . DB_USER . " -p" . DB_PASS . " " . DB_NAME . " < " . escapeshellarg($filepath) . " 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            logActivity('database_restore', 'settings', null, null, ['file' => basename($filepath)]);
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Restore failed: ' . implode("\n", $output)];
    }

    public function getBackups() {
        $backupDir = BACKUP_PATH;
        if (!is_dir($backupDir)) return [];

        $files = glob($backupDir . '/*.sql');
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => filesize($file),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }
        usort($backups, fn($a, $b) => strcmp($b['date'], $a['date']));
        return $backups;
    }
}
