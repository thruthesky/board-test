<?php

namespace lib\comment;

/**
 * 댓글 관련 HTTP 요청을 처리하는 컨트롤러
 */
class CommentController
{
    private CommentService $service;

    public function __construct()
    {
        $this->service = new CommentService();
    }

    /**
     * 댓글 작성 (API)
     */
    public function create(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $content = $_POST['content'] ?? '';
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        return $this->service->create($postId, $_SESSION['user_id'], $content, $parentId);
    }

    /**
     * 게시글의 댓글 목록 (API)
     */
    public function list(): array
    {
        $postId = (int)($_GET['post_id'] ?? 0);
        $comments = $this->service->getCommentsByPostId($postId);

        return [
            'success' => true,
            'comments' => array_map(fn($c) => $c->toArray(), $comments),
            'total_count' => $this->service->getCommentCount($postId),
        ];
    }

    /**
     * 댓글 수정 (API)
     */
    public function update(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $commentId = (int)($_POST['id'] ?? 0);
        $content = $_POST['content'] ?? '';

        return $this->service->update($commentId, $_SESSION['user_id'], $content);
    }

    /**
     * 댓글 삭제 (API)
     */
    public function delete(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $commentId = (int)($_POST['id'] ?? 0);

        return $this->service->delete($commentId, $_SESSION['user_id']);
    }
}
