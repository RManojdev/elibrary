<?php
class DbSessionHandler implements SessionHandlerInterface
{
    private mysqli $db;
    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }
    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $stmt = $this->db->prepare("SELECT data FROM php_sessions WHERE id=? LIMIT 1");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ? $row['data'] : '';
    }

    public function write(string $id, string $data): bool
    {
        $now = time();
        $stmt = $this->db->prepare("INSERT INTO php_sessions (id,data,last_activity) VALUES (?,?,?) ON DUPLICATE KEY UPDATE data=VALUES(data), last_activity=VALUES(last_activity)");
        $stmt->bind_param('ssi', $id, $data, $now);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM php_sessions WHERE id=?");
        $stmt->bind_param('s', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function gc(int $max_lifetime): int|false
    {
        $cut = time() - $max_lifetime;
        $stmt = $this->db->prepare("DELETE FROM php_sessions WHERE last_activity < ?");
        $stmt->bind_param('i', $cut);
        $stmt->execute();
        $n = $stmt->affected_rows;
        $stmt->close();
        return $n;
    }
}