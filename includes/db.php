<?php
/**
 * Database Connection and Initialization
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            // Create data directory if it doesn't exist
            $dataDir = dirname(DB_PATH);
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0755, true);
            }
            
            // Create SQLite connection
            $this->pdo = new PDO('sqlite:' . DB_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Initialize tables
            $this->initTables();
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    private function initTables() {
        // Create short_urls table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS short_urls (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL UNIQUE,
                target TEXT NOT NULL,
                redirect_type INTEGER DEFAULT 302,
                clicks INTEGER DEFAULT 0,
                last_click_at TEXT,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");
        
        // Create clicks table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS clicks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL,
                target TEXT NOT NULL,
                ip TEXT NOT NULL,
                user_agent TEXT NOT NULL,
                referer TEXT,
                created_at TEXT NOT NULL
            )
        ");
        
        // Create indexes
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_code ON short_urls(code)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_clicks_code ON clicks(code)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_clicks_created ON clicks(created_at)");
    }
}

// Get database instance
function getDB() {
    return Database::getInstance()->getConnection();
}
