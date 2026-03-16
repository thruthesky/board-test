<?php

namespace lib\upload;

use Utils\Db;

/**
 * uploads 테이블에 대한 CRUD 작업을 수행하는 리포지토리 클래스
 */
class UploadRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance()->getPdo();
    }

    /**
     * 업로드 레코드 생성
     */
    public function create(UploadEntity $upload): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO uploads (user_id, original_name, stored_name, path, mime_type, file_size)
            VALUES (:user_id, :original_name, :stored_name, :path, :mime_type, :file_size)
        ');
        $stmt->execute([
            'user_id' => $upload->user_id,
            'original_name' => $upload->original_name,
            'stored_name' => $upload->stored_name,
            'path' => $upload->path,
            'mime_type' => $upload->mime_type,
            'file_size' => $upload->file_size,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * ID로 업로드 파일 조회
     */
    public function findById(int $id): ?UploadEntity
    {
        $stmt = $this->pdo->prepare('SELECT * FROM uploads WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new UploadEntity($row) : null;
    }

    /**
     * 사용자 ID로 업로드 파일 목록 조회
     */
    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM uploads WHERE user_id = :user_id ORDER BY id DESC');
        $stmt->execute(['user_id' => $userId]);
        return array_map(fn($row) => new UploadEntity($row), $stmt->fetchAll());
    }

    /**
     * 업로드 파일 삭제
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM uploads WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
