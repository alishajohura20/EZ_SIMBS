<?php
require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    /*
     * Nested-transaction support via savepoints.
     *
     * PDO's MySQL driver throws "There is already an active transaction"
     * when beginTransaction() is called on a connection that is already
     * inside one. Composite operations like SaleReturn::exchange() (which
     * runs its own transaction AND delegates to Sale::create(), itself a
     * transaction) therefore compose through savepoints: an inner
     * beginTransaction() becomes SAVEPOINT, the matching commit() is a
     * no-op, and the matching rollBack() unwinds to that savepoint only.
     * A top-level (or fully-nested-throw) rollBack() still aborts the
     * whole outermost transaction, so nothing ever half-commits.
     */
    private $txDepth = 0;

    public function beginTransaction() {
        if ($this->conn->inTransaction()) {
            $this->txDepth++;
            $this->conn->exec("SAVEPOINT ez_sp_{$this->txDepth}");
            return;
        }
        $this->conn->beginTransaction();
        $this->txDepth = 0;
    }

    public function commit() {
        if ($this->txDepth > 0) {
            $this->txDepth--;
            return;
        }
        $this->conn->commit();
    }

    public function rollBack() {
        if ($this->txDepth > 0) {
            $this->conn->exec("ROLLBACK TO SAVEPOINT ez_sp_{$this->txDepth}");
            $this->txDepth--;
            return;
        }
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->conn->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }

    public function count($table, $where = '1', $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$table} WHERE {$where}";
        return $this->fetch($sql, $params)['total'];
    }
}
