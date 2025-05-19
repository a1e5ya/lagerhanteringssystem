<?php
// includes/Database.php

class Database {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function selectData($table, $where = null, $params = [], $order = null, $limit = null) {
        $sql = "SELECT * FROM {$table}";
        if ($where) $sql .= " WHERE {$where}";
        if ($order) $sql .= " ORDER BY {$order}";
        if ($limit) $sql .= " LIMIT {$limit}";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insertData($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->pdo->lastInsertId();
    }
    
    public function updateData($table, $id, $data, $idField = null) {
        $idField = $idField ?? $table . '_id';
        $setClause = array_map(function($key) {
            return "{$key} = ?";
        }, array_keys($data));
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setClause) . " WHERE {$idField} = ?";
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function deleteData($table, $id, $idField = null) {
        $idField = $idField ?? $table . '_id';
        $sql = "DELETE FROM {$table} WHERE {$idField} = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function executeQuery($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function backupDatabase($backupPath = null) {
        if (!$backupPath) {
            // Updated to use routing system
            $backupPath = Routes::getBasePath() . '/backups/';
        }
        
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        $timestamp = date('Y-m-d_His');
        $filename = "karis_inventory_backup_{$timestamp}.sql";
        $filepath = $backupPath . $filename;
        
        $dbname = $this->pdo->query('SELECT DATABASE()')->fetchColumn();
        $host = $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        $host = explode(' ', $host)[1] ?? 'localhost';
        
        // Get DB credentials from config instead of hardcoding
        global $user, $pass;
        
        $command = "mysqldump --host={$host} --user={$user} --password={$pass} {$dbname} > {$filepath}";
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            // Compress backup
            $zipCommand = "gzip {$filepath}";
            exec($zipCommand);
            
            // Log the backup
            $this->logBackup($filename . '.gz');
            return ['success' => true, 'filename' => $filename . '.gz'];
        }
        
        return ['success' => false, 'message' => 'Backup failed'];
    }
    
    private function logBackup($filename) {
        // Use existing event_log table
        $sql = "INSERT INTO event_log (user_id, event_type, event_description) VALUES (?, ?, ?)";
        $userId = $_SESSION['user_id'] ?? 1;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, 'database_backup', "Database backup created: {$filename}"]);
    }
}