<?php
class ReturnPhoto {
    private $db;
    private $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    private $maxBytes = 4194304;   // 4 MB per file
    private $maxPerRequest = 5;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** Normalise $_FILES['photos'] (single or multi) into a flat list. */
    private function flatten($files) {
        if (!isset($files['name'])) return [];
        if (!is_array($files['name'])) return [$files];

        $out = [];
        foreach (array_keys($files['name']) as $i) {
            $out[] = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
        }
        return $out;
    }

    private function store($kind, $returnId, $itemId, $file, $userId) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowed, true)) return null;
        if ((int)$file['size'] > $this->maxBytes) return null;

        $dir = UPLOAD_PATH . '/returns/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $filename = "ret_{$kind}_{$returnId}_" . uniqid() . ".{$ext}";
        $dest = $dir . $filename;

        if (is_uploaded_file($file['tmp_name'])) {
            $ok = move_uploaded_file($file['tmp_name'], $dest);
        } else {
            $ok = rename($file['tmp_name'], $dest);
            if (!$ok) $ok = copy($file['tmp_name'], $dest) && unlink($file['tmp_name']);
        }
        if (!$ok) return null;

        $rel = 'uploads/returns/' . $filename;
        $this->db->insert('return_photos', [
            'return_kind'   => $kind,
            'return_id'     => $returnId,
            'return_item_id'=> $itemId,
            'path'          => $rel,
            'uploaded_by'   => $userId,
        ]);
        return $rel;
    }

    public function addMany($kind, $returnId, $itemId, $files, $userId) {
        $saved = [];
        foreach (array_slice($this->flatten($files), 0, $this->maxPerRequest) as $f) {
            $p = $this->store($kind, $returnId, $itemId, $f, $userId);
            if ($p) $saved[] = $p;
        }
        return $saved;
    }

    public function getFor($kind, $returnId) {
        return $this->db->fetchAll(
            "SELECT rp.*, u.name as uploaded_by_name
             FROM return_photos rp
             LEFT JOIN users u ON rp.uploaded_by = u.id
             WHERE rp.return_kind = ? AND rp.return_id = ?
             ORDER BY rp.id", [$kind, $returnId]
        );
    }

    public function delete($photoId) {
        $p = $this->db->fetch("SELECT * FROM return_photos WHERE id = ?", [$photoId]);
        if (!$p) return false;
        $abs = UPLOAD_PATH . '/' . basename(dirname($p['path'])) . '/' . basename($p['path']);
        if (is_file($abs)) unlink($abs);
        $this->db->delete('return_photos', 'id = ?', [$photoId]);
        return true;
    }
}