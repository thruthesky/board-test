<?php

namespace lib\post;

/**
 * 게시글 관련 비즈니스 로직을 처리하는 서비스 클래스
 */
class PostService
{
    private PostRepository $repository;

    public function __construct()
    {
        $this->repository = new PostRepository();
    }

    // 허용된 카테고리 목록
    public const CATEGORIES = ['discussion', 'qna', 'news'];

    /**
     * 게시글 작성
     */
    public function create(int $userId, string $title, string $content, string $category = 'discussion'): array
    {
        if (empty($title) || empty($content)) {
            return ['success' => false, 'message' => '제목과 내용을 입력해주세요.'];
        }

        if (mb_strlen($title) > 200) {
            return ['success' => false, 'message' => '제목은 200자 이내로 입력해주세요.'];
        }

        if (!in_array($category, self::CATEGORIES, true)) {
            return ['success' => false, 'message' => '유효하지 않은 카테고리입니다.'];
        }

        $post = new PostEntity([
            'user_id' => $userId,
            'category' => $category,
            'title' => $title,
            'content' => $content,
        ]);

        $postId = $this->repository->create($post);

        return ['success' => true, 'message' => '게시글이 작성되었습니다.', 'post_id' => $postId];
    }

    /**
     * 게시글 목록 조회
     */
    public function getList(int $page = 1, int $perPage = 10): array
    {
        $posts = $this->repository->findAll($page, $perPage);
        $totalCount = $this->repository->countAll();
        $totalPages = max(1, (int)ceil($totalCount / $perPage));

        return [
            'posts' => $posts,
            'total_count' => $totalCount,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
        ];
    }

    /**
     * 게시글 상세 조회
     */
    public function getPost(int $id): ?PostEntity
    {
        return $this->repository->findById($id);
    }

    /**
     * 카테고리별 게시글 목록 조회
     */
    public function getListByCategory(string $category, int $page = 1, int $perPage = 10): array
    {
        $posts = $this->repository->findByCategory($category, $page, $perPage);
        $totalCount = $this->repository->countByCategory($category);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));

        return [
            'posts' => $posts,
            'total_count' => $totalCount,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'category' => $category,
        ];
    }

    /**
     * 게시글 수정
     */
    public function update(int $postId, int $userId, string $title, string $content): array
    {
        if (empty($title) || empty($content)) {
            return ['success' => false, 'message' => '제목과 내용을 입력해주세요.'];
        }

        if (mb_strlen($title) > 200) {
            return ['success' => false, 'message' => '제목은 200자 이내로 입력해주세요.'];
        }

        $post = $this->repository->findById($postId);
        if ($post === null) {
            return ['success' => false, 'message' => '게시글을 찾을 수 없습니다.'];
        }

        // 작성자 확인
        if ($post->user_id !== $userId) {
            return ['success' => false, 'message' => '수정 권한이 없습니다.'];
        }

        $post->title = $title;
        $post->content = $content;
        $this->repository->update($post);

        return ['success' => true, 'message' => '게시글이 수정되었습니다.'];
    }

    /**
     * 게시글 삭제
     */
    public function delete(int $postId, int $userId): array
    {
        $post = $this->repository->findById($postId);
        if ($post === null) {
            return ['success' => false, 'message' => '게시글을 찾을 수 없습니다.'];
        }

        // 작성자 확인
        if ($post->user_id !== $userId) {
            return ['success' => false, 'message' => '삭제 권한이 없습니다.'];
        }

        $this->repository->delete($postId);

        return ['success' => true, 'message' => '게시글이 삭제되었습니다.'];
    }
}
