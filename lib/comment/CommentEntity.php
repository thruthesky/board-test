<?php

namespace lib\comment;

/**
 * comments 테이블과 매핑되는 엔티티 클래스
 */
class CommentEntity
{
    public ?int $id;
    public int $post_id;
    public int $user_id;
    public ?int $parent_id;
    public string $content;
    public ?string $created_at;
    public ?string $updated_at;

    // JOIN으로 가져온 작성자 이름
    public ?string $author_name;

    // 대댓글 목록
    public array $children = [];

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->post_id = $data['post_id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->parent_id = $data['parent_id'] ?? null;
        $this->content = $data['content'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->author_name = $data['author_name'] ?? null;
    }

    /**
     * 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'content' => $this->content,
            'author_name' => $this->author_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => array_map(fn($c) => $c->toArray(), $this->children),
        ];
    }
}
