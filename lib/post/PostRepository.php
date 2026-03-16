<?php

namespace lib\post;

use Utils\Db;

/**
 * posts 테이블에 대한 CRUD 작업을 수행하는 리포지토리 클래스
 */
class PostRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance()->getPdo();
    }

    /**
     * 게시글 생성
     */
    public function create(PostEntity $post): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO posts (user_id, category, title, content) VALUES (:user_id, :category, :title, :content)
        ');
        $stmt->execute([
            'user_id' => $post->user_id,
            'category' => $post->category,
            'title' => $post->title,
            'content' => $post->content,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * ID로 게시글 조회 (작성자 이름 포함)
     */
    public function findById(int $id): ?PostEntity
    {
        $stmt = $this->pdo->prepare('
            SELECT p.*, u.name as author_name, up.path as author_photo_path
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN uploads up ON u.profile_photo_id = up.id
            WHERE p.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new PostEntity($row) : null;
    }

    /**
     * 게시글 목록 조회 (페이징)
     */
    public function findAll(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare('
            SELECT p.*, u.name as author_name, up.path as author_photo_path
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN uploads up ON u.profile_photo_id = up.id
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return array_map(fn($row) => new PostEntity($row), $rows);
    }

    /**
     * 전체 게시글 수 조회
     */
    public function countAll(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM posts');
        return (int)$stmt->fetchColumn();
    }

    /**
     * 카테고리별 게시글 목록 조회 (페이징)
     */
    public function findByCategory(string $category, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare('
            SELECT p.*, u.name as author_name, up.path as author_photo_path
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN uploads up ON u.profile_photo_id = up.id
            WHERE p.category = :category
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue('category', $category);
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return array_map(fn($row) => new PostEntity($row), $rows);
    }

    /**
     * 카테고리별 게시글 수 조회
     */
    public function countByCategory(string $category): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM posts WHERE category = :category');
        $stmt->execute(['category' => $category]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * 게시글 수정
     */
    public function update(PostEntity $post): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE posts SET title = :title, content = :content, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        return $stmt->execute([
            'title' => $post->title,
            'content' => $post->content,
            'id' => $post->id,
        ]);
    }

    /**
     * 게시글 삭제
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
