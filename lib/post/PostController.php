<?php

namespace lib\post;

/**
 * 게시글 관련 HTTP 요청을 처리하는 컨트롤러
 */
class PostController
{
    private PostService $service;

    public function __construct()
    {
        $this->service = new PostService();
    }

    /**
     * 게시글 작성 (API)
     */
    public function create(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        return $this->service->create($_SESSION['user_id'], $title, $content);
    }

    /**
     * 게시글 목록 (API)
     */
    public function list(): array
    {
        $page = (int)($_GET['page'] ?? 1);
        $data = $this->service->getList($page);

        return [
            'success' => true,
            'posts' => array_map(fn($p) => $p->toArray(), $data['posts']),
            'total_count' => $data['total_count'],
            'current_page' => $data['current_page'],
            'total_pages' => $data['total_pages'],
        ];
    }

    /**
     * 게시글 상세 (API)
     */
    public function view(): array
    {
        $id = (int)($_GET['id'] ?? 0);
        $post = $this->service->getPost($id);

        if ($post === null) {
            return ['success' => false, 'message' => '게시글을 찾을 수 없습니다.'];
        }

        return ['success' => true, 'post' => $post->toArray()];
    }

    /**
     * 게시글 수정 (API)
     */
    public function update(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $id = (int)($_POST['id'] ?? 0);
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        return $this->service->update($id, $_SESSION['user_id'], $title, $content);
    }

    /**
     * 게시글 삭제 (API)
     */
    public function delete(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $id = (int)($_POST['id'] ?? 0);

        return $this->service->delete($id, $_SESSION['user_id']);
    }
}
