<?php

namespace lib\user;

/**
 * users 테이블과 매핑되는 엔티티 클래스
 */
class UserEntity
{
    public ?int $id;
    public string $name;
    public string $email;
    public string $password;
    public ?int $profile_photo_id;
    public ?string $created_at;
    public ?string $updated_at;

    // JOIN으로 가져온 프로필 사진 경로
    public ?string $profile_photo_path;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->profile_photo_id = $data['profile_photo_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->profile_photo_path = $data['profile_photo_path'] ?? null;
    }

    /**
     * 배열로 변환 (비밀번호 제외)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_photo_id' => $this->profile_photo_id,
            'profile_photo_url' => $this->getProfilePhotoUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * 프로필 사진 URL 반환
     */
    public function getProfilePhotoUrl(): ?string
    {
        if ($this->profile_photo_path) {
            return '/' . $this->profile_photo_path;
        }
        return null;
    }
}
