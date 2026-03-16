<?php

namespace lib\user;

/**
 * 회원 관련 비즈니스 로직을 처리하는 서비스 클래스
 */
class UserService
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    /**
     * 회원 가입
     * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
     */
    public function register(string $name, string $email, string $password, string $passwordConfirm): array
    {
        // 입력값 검증
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => '모든 필드를 입력해주세요.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => '유효한 이메일 주소를 입력해주세요.'];
        }

        if (strlen($password) < 4) {
            return ['success' => false, 'message' => '비밀번호는 4자 이상이어야 합니다.'];
        }

        if ($password !== $passwordConfirm) {
            return ['success' => false, 'message' => '비밀번호가 일치하지 않습니다.'];
        }

        // 이메일 중복 확인
        if ($this->repository->findByEmail($email) !== null) {
            return ['success' => false, 'message' => '이미 사용 중인 이메일입니다.'];
        }

        // 비밀번호 해싱 후 저장
        $user = new UserEntity([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $userId = $this->repository->create($user);

        return ['success' => true, 'message' => '회원 가입이 완료되었습니다.', 'user_id' => $userId];
    }

    /**
     * 로그인
     */
    public function login(string $email, string $password): array
    {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => '이메일과 비밀번호를 입력해주세요.'];
        }

        $user = $this->repository->findByEmail($email);
        if ($user === null || !password_verify($password, $user->password)) {
            return ['success' => false, 'message' => '이메일 또는 비밀번호가 올바르지 않습니다.'];
        }

        // 세션에 사용자 정보 저장
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_profile_photo_url'] = $user->getProfilePhotoUrl();

        return ['success' => true, 'message' => '로그인 성공', 'user' => $user->toArray()];
    }

    /**
     * 로그아웃
     */
    public function logout(): void
    {
        session_destroy();
    }

    /**
     * 회원 정보 수정
     */
    public function update(int $userId, string $name, string $email): array
    {
        if (empty($name) || empty($email)) {
            return ['success' => false, 'message' => '이름과 이메일을 입력해주세요.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => '유효한 이메일 주소를 입력해주세요.'];
        }

        // 이메일 중복 확인 (본인 제외)
        $existing = $this->repository->findByEmail($email);
        if ($existing !== null && $existing->id !== $userId) {
            return ['success' => false, 'message' => '이미 사용 중인 이메일입니다.'];
        }

        $user = $this->repository->findById($userId);
        if ($user === null) {
            return ['success' => false, 'message' => '사용자를 찾을 수 없습니다.'];
        }

        $user->name = $name;
        $user->email = $email;
        $this->repository->update($user);

        // 세션 정보 갱신
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        return ['success' => true, 'message' => '회원 정보가 수정되었습니다.'];
    }

    /**
     * 비밀번호 변경
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword, string $newPasswordConfirm): array
    {
        if (empty($currentPassword) || empty($newPassword)) {
            return ['success' => false, 'message' => '모든 비밀번호 필드를 입력해주세요.'];
        }

        if (strlen($newPassword) < 4) {
            return ['success' => false, 'message' => '새 비밀번호는 4자 이상이어야 합니다.'];
        }

        if ($newPassword !== $newPasswordConfirm) {
            return ['success' => false, 'message' => '새 비밀번호가 일치하지 않습니다.'];
        }

        $user = $this->repository->findById($userId);
        if ($user === null) {
            return ['success' => false, 'message' => '사용자를 찾을 수 없습니다.'];
        }

        if (!password_verify($currentPassword, $user->password)) {
            return ['success' => false, 'message' => '현재 비밀번호가 올바르지 않습니다.'];
        }

        $this->repository->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));

        return ['success' => true, 'message' => '비밀번호가 변경되었습니다.'];
    }

    /**
     * 현재 로그인한 사용자 정보 조회
     */
    public function getCurrentUser(): ?UserEntity
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return $this->repository->findById($_SESSION['user_id']);
    }

    /**
     * 로그인 여부 확인
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * 프로필 사진 변경
     */
    public function updateProfilePhoto(int $userId, int $uploadId): array
    {
        $uploadService = new \lib\upload\UploadService();
        $upload = $uploadService->getUpload($uploadId);

        if ($upload === null) {
            return ['success' => false, 'message' => '파일을 찾을 수 없습니다.'];
        }

        if ($upload->user_id !== $userId) {
            return ['success' => false, 'message' => '본인이 업로드한 파일만 프로필 사진으로 설정할 수 있습니다.'];
        }

        if (!$upload->isImage()) {
            return ['success' => false, 'message' => '이미지 파일만 프로필 사진으로 설정할 수 있습니다.'];
        }

        $this->repository->updateProfilePhoto($userId, $uploadId);

        // 세션에 프로필 사진 URL 저장
        $_SESSION['user_profile_photo_url'] = $upload->getUrl();

        return [
            'success' => true,
            'message' => '프로필 사진이 변경되었습니다.',
            'profile_photo_url' => $upload->getUrl(),
        ];
    }
}
