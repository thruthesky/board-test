<?php

namespace lib\comment;

use Utils\Db;

/**
 * comments 테이블에 대한 CRUD 작업을 수행하는 리포지토리 클래스
 */
class CommentRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance()->getPdo();
    }

    /**
     * 댓글 생성
     */
    public function create(CommentEntity $comment): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO comments (post_id, user_id, parent_id, content)
            VALUES (:post_id, :user_id, :parent_id, :content)
        ');
        $stmt->execute([
            'post_id' => $comment->post_id,
            'user_id' => $comment->user_id,
            'parent_id' => $comment->parent_id,
            'content' => $comment->content,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * ID로 댓글 조회
     */
    public function findById(int $id): ?CommentEntity
    {
        $stmt = $this->pdo->prepare('
            SELECT c.*, u.name as author_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new CommentEntity($row) : null;
    }

    /**
     * 게시글의 모든 댓글 조회 (작성자 이름 포함)
     */
    public function findByPostId(int $postId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.*, u.name as author_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = :post_id
            ORDER BY c.created_at ASC
        ');
        $stmt->execute(['post_id' => $postId]);
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => new CommentEntity($row), $rows);
    }

    /**
     * 게시글의 댓글 수 조회
     */
    public function countByPostId(int $postId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM comments WHERE post_id = :post_id');
        $stmt->execute(['post_id' => $postId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * 댓글 수정
     */
    public function update(CommentEntity $comment): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE comments SET content = :content, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        return $stmt->execute([
            'content' => $comment->content,
            'id' => $comment->id,
        ]);
    }

    /**
     * 댓글 삭제
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
