<?php

namespace lib\comment;

use lib\post\PostRepository;

/**
 * 댓글 관련 비즈니스 로직을 처리하는 서비스 클래스
 */
class CommentService
{
    private CommentRepository $repository;
    private PostRepository $postRepository;

    public function __construct()
    {
        $this->repository = new CommentRepository();
        $this->postRepository = new PostRepository();
    }

    /**
     * 댓글 작성
     */
    public function create(int $postId, int $userId, string $content, ?int $parentId = null): array
    {
        if (empty(trim($content))) {
            return ['success' => false, 'message' => '댓글 내용을 입력해주세요.'];
        }

        if (mb_strlen($content) > 1000) {
            return ['success' => false, 'message' => '댓글은 1000자 이내로 입력해주세요.'];
        }

        // 게시글 존재 확인
        $post = $this->postRepository->findById($postId);
        if ($post === null) {
            return ['success' => false, 'message' => '게시글을 찾을 수 없습니다.'];
        }

        // 대댓글인 경우 부모 댓글 확인
        if ($parentId !== null) {
            $parentComment = $this->repository->findById($parentId);
            if ($parentComment === null) {
                return ['success' => false, 'message' => '부모 댓글을 찾을 수 없습니다.'];
            }
            // 부모 댓글이 같은 게시글에 속하는지 확인
            if ($parentComment->post_id !== $postId) {
                return ['success' => false, 'message' => '잘못된 부모 댓글입니다.'];
            }
        }

        $comment = new CommentEntity([
            'post_id' => $postId,
            'user_id' => $userId,
            'parent_id' => $parentId,
            'content' => $content,
        ]);

        $commentId = $this->repository->create($comment);

        return ['success' => true, 'message' => '댓글이 작성되었습니다.', 'comment_id' => $commentId];
    }

    /**
     * 게시글의 댓글 목록 조회 (트리 구조)
     */
    public function getCommentsByPostId(int $postId): array
    {
        $allComments = $this->repository->findByPostId($postId);

        // 트리 구조로 변환: 무한 깊이 지원
        $commentMap = [];
        foreach ($allComments as $comment) {
            $comment->children = [];
            $commentMap[$comment->id] = $comment;
        }

        $rootComments = [];
        foreach ($commentMap as $comment) {
            if ($comment->parent_id === null) {
                $rootComments[] = $comment;
            } elseif (isset($commentMap[$comment->parent_id])) {
                $commentMap[$comment->parent_id]->children[] = $comment;
            }
        }

        return $rootComments;
    }

    /**
     * 댓글 수정
     */
    public function update(int $commentId, int $userId, string $content): array
    {
        if (empty(trim($content))) {
            return ['success' => false, 'message' => '댓글 내용을 입력해주세요.'];
        }

        if (mb_strlen($content) > 1000) {
            return ['success' => false, 'message' => '댓글은 1000자 이내로 입력해주세요.'];
        }

        $comment = $this->repository->findById($commentId);
        if ($comment === null) {
            return ['success' => false, 'message' => '댓글을 찾을 수 없습니다.'];
        }

        if ($comment->user_id !== $userId) {
            return ['success' => false, 'message' => '수정 권한이 없습니다.'];
        }

        $comment->content = $content;
        $this->repository->update($comment);

        return ['success' => true, 'message' => '댓글이 수정되었습니다.'];
    }

    /**
     * 댓글 삭제
     */
    public function delete(int $commentId, int $userId): array
    {
        $comment = $this->repository->findById($commentId);
        if ($comment === null) {
            return ['success' => false, 'message' => '댓글을 찾을 수 없습니다.'];
        }

        if ($comment->user_id !== $userId) {
            return ['success' => false, 'message' => '삭제 권한이 없습니다.'];
        }

        $this->repository->delete($commentId);

        return ['success' => true, 'message' => '댓글이 삭제되었습니다.'];
    }

    /**
     * 게시글의 댓글 수 조회
     */
    public function getCommentCount(int $postId): int
    {
        return $this->repository->countByPostId($postId);
    }
}
