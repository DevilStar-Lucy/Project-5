<?php
// Database configuration for Supabase
require_once 'supabase_helper.php';

// Initialize Supabase connection
$supabase = new SupabaseHelper();

// Legacy compatibility layer for existing MySQL code
class DatabaseAdapter {
    private $supabase;
    private $last_insert_id;
    
    public function __construct($supabase_helper) {
        $this->supabase = $supabase_helper;
        $this->last_insert_id = null;
    }
    
    public function query($sql) {
        // Parse basic SQL queries and convert to Supabase API calls
        // This is a simplified implementation - you may need to enhance it
        
        if (preg_match('/SELECT \* FROM (\w+)/', $sql, $matches)) {
            $table = $matches[1];
            return new QueryResult($this->supabase->makeRequest($table . '?select=*'));
        }
        
        if (preg_match('/INSERT INTO (\w+) SET (.+)/', $sql, $matches)) {
            $table = $matches[1];
            $data = $this->parseSetClause($matches[2]);
            $result = $this->supabase->makeRequest($table, 'POST', $data);
            if ($result && isset($result[0]['id'])) {
                $this->last_insert_id = $result[0]['id'];
            }
            return new QueryResult($result);
        }
        
        // Add more SQL parsing as needed
        return new QueryResult([]);
    }
    
    private function parseSetClause($setClause) {
        $data = [];
        $pairs = explode(',', $setClause);
        
        foreach ($pairs as $pair) {
            if (preg_match('/(\w+)\s*=\s*[\'"]([^\'"]*)[\'"]/', trim($pair), $matches)) {
                $data[$matches[1]] = $matches[2];
            } elseif (preg_match('/(\w+)\s*=\s*(\d+(?:\.\d+)?)/', trim($pair), $matches)) {
                $data[$matches[1]] = is_numeric($matches[2]) ? 
                    (strpos($matches[2], '.') !== false ? floatval($matches[2]) : intval($matches[2])) : 
                    $matches[2];
            }
        }
        
        return $data;
    }
    
    public function getLastInsertId() {
        return $this->last_insert_id;
    }
}

class QueryResult {
    private $data;
    private $index = 0;
    
    public function __construct($data) {
        $this->data = is_array($data) ? $data : [];
    }
    
    public function fetch_assoc() {
        if ($this->index < count($this->data)) {
            return $this->data[$this->index++];
        }
        return null;
    }
    
    public function num_rows() {
        return count($this->data);
    }
}

// Create global database adapter
$conn = new DatabaseAdapter($supabase);

// Legacy function compatibility
function mysqli_query($conn, $sql) {
    return $conn->query($sql);
}

function mysqli_fetch_assoc($result) {
    return $result->fetch_assoc();
}

function mysqli_num_rows($result) {
    return $result->num_rows();
}

function mysqli_insert_id($conn) {
    return $conn->getLastInsertId();
}

function mysqli_real_escape_string($conn, $string) {
    return addslashes($string);
}

function mysqli_error($conn) {
    return "Database error";
}
?>