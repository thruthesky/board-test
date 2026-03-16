<?php

namespace lib\user;

use Utils\Db;

/**
 * users 테이블에 대한 CRUD 작업을 수행하는 리포지토리 클래스
 */
class UserRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Db::getInstance()->getPdo();
    }

    /**
     * 회원 생성
     */
    public function create(UserEntity $user): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO users (name, email, password) VALUES (:name, :email, :password)
        ');
        $stmt->execute([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * ID로 회원 조회 (프로필 사진 경로 포함)
     */
    public function findById(int $id): ?UserEntity
    {
        $stmt = $this->pdo->prepare('
            SELECT u.*, up.path as profile_photo_path
            FROM users u
            LEFT JOIN uploads up ON u.profile_photo_id = up.id
            WHERE u.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new UserEntity($row) : null;
    }

    /**
     * 이메일로 회원 조회
     */
    public function findByEmail(string $email): ?UserEntity
    {
        $stmt = $this->pdo->prepare('
            SELECT u.*, up.path as profile_photo_path
            FROM users u
            LEFT JOIN uploads up ON u.profile_photo_id = up.id
            WHERE u.email = :email
        ');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? new UserEntity($row) : null;
    }

    /**
     * 회원 정보 수정
     */
    public function update(UserEntity $user): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE users SET name = :name, email = :email, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        return $stmt->execute([
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
        ]);
    }

    /**
     * 비밀번호 변경
     */
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE users SET password = :password, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $id,
        ]);
    }

    /**
     * 프로필 사진 ID 업데이트
     */
    public function updateProfilePhoto(int $userId, ?int $photoId): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE users SET profile_photo_id = :photo_id, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        return $stmt->execute([
            'photo_id' => $photoId,
            'id' => $userId,
        ]);
    }
}
