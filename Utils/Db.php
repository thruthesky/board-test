<?php

namespace Utils;

/**
 * 데이터베이스 연결 및 초기화를 담당하는 유틸리티 클래스
 */
class Db
{
    private static ?Db $instance = null;
    private \PDO $pdo;

    private function __construct(bool $inMemory = false)
    {
        if ($inMemory) {
            $this->pdo = new \PDO('sqlite::memory:');
        } else {
            $dbPath = __DIR__ . '/../data/database.sqlite';
            $this->pdo = new \PDO('sqlite:' . $dbPath);
        }
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        // WAL 모드로 성능 향상 (파일 DB일 때만)
        if (!$inMemory) {
            $this->pdo->exec('PRAGMA journal_mode=WAL');
        }
        $this->pdo->exec('PRAGMA foreign_keys=ON');
        $this->initTables();
    }

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 테스트용 인메모리 DB 인스턴스 생성 및 설정
     */
    public static function createInMemory(): self
    {
        self::$instance = new self(true);
        return self::$instance;
    }

    /**
     * 싱글톤 인스턴스 초기화 (테스트 후 정리용)
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * PDO 인스턴스 반환
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * 테이블 초기화
     */
    private function initTables(): void
    {
        // users 테이블 생성
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // posts 테이블 생성
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                category TEXT NOT NULL DEFAULT \'discussion\',
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');

        // uploads 테이블 생성
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS uploads (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                original_name TEXT NOT NULL,
                stored_name TEXT NOT NULL,
                path TEXT NOT NULL,
                mime_type TEXT NOT NULL,
                file_size INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');

        // comments 테이블 생성
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                parent_id INTEGER DEFAULT NULL,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
            )
        ');

        // 마이그레이션: users 테이블에 profile_photo_id 컬럼 추가
        $columns = $this->pdo->query('PRAGMA table_info(users)')->fetchAll();
        $columnNames = array_column($columns, 'name');
        if (!in_array('profile_photo_id', $columnNames, true)) {
            $this->pdo->exec('ALTER TABLE users ADD COLUMN profile_photo_id INTEGER DEFAULT NULL');
        }
    }
}
