<?php

namespace lib\post;

/**
 * posts 테이블과 매핑되는 엔티티 클래스
 */
class PostEntity
{
    public ?int $id;
    public int $user_id;
    public string $category;
    public string $title;
    public string $content;
    public ?string $created_at;
    public ?string $updated_at;

    // JOIN으로 가져온 작성자 이름과 아바타
    public ?string $author_name;
    public ?string $author_photo_path;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? 0;
        $this->category = $data['category'] ?? 'discussion';
        $this->title = $data['title'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->author_name = $data['author_name'] ?? null;
        $this->author_photo_path = $data['author_photo_path'] ?? null;
    }

    /**
     * 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category' => $this->category,
            'title' => $this->title,
            'content' => $this->content,
            'author_name' => $this->author_name,
            'author_photo_url' => $this->getAuthorPhotoUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * 작성자 프로필 사진 URL
     */
    public function getAuthorPhotoUrl(): ?string
    {
        return $this->author_photo_path ? '/' . $this->author_photo_path : null;
    }
}
