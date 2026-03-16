<?php

namespace lib\upload;

/**
 * uploads 테이블과 매핑되는 엔티티 클래스
 */
class UploadEntity
{
    public ?int $id;
    public int $user_id;
    public string $original_name;
    public string $stored_name;
    public string $path;
    public string $mime_type;
    public int $file_size;
    public ?string $created_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? 0;
        $this->original_name = $data['original_name'] ?? '';
        $this->stored_name = $data['stored_name'] ?? '';
        $this->path = $data['path'] ?? '';
        $this->mime_type = $data['mime_type'] ?? '';
        $this->file_size = $data['file_size'] ?? 0;
        $this->created_at = $data['created_at'] ?? null;
    }

    /**
     * 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'original_name' => $this->original_name,
            'stored_name' => $this->stored_name,
            'path' => $this->path,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'url' => $this->getUrl(),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * 파일 접근 URL 반환
     */
    public function getUrl(): string
    {
        return '/' . $this->path;
    }

    /**
     * 이미지 파일 여부
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * 동영상 파일 여부
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }
}
