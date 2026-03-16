<?php

namespace lib\user;

/**
 * 회원 관련 HTTP 요청을 처리하는 컨트롤러
 */
class UserController
{
    private UserService $service;

    public function __construct()
    {
        $this->service = new UserService();
    }

    /**
     * 회원 가입 처리 (API)
     */
    public function register(): array
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        return $this->service->register($name, $email, $password, $passwordConfirm);
    }

    /**
     * 로그인 처리 (API)
     */
    public function login(): array
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        return $this->service->login($email, $password);
    }

    /**
     * 로그아웃 처리 (API)
     */
    public function logout(): array
    {
        $this->service->logout();
        return ['success' => true, 'message' => '로그아웃 되었습니다.'];
    }

    /**
     * 회원 정보 수정 (API)
     */
    public function update(): array
    {
        if (!$this->service->isLoggedIn()) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';

        return $this->service->update($_SESSION['user_id'], $name, $email);
    }

    /**
     * 비밀번호 변경 (API)
     */
    public function changePassword(): array
    {
        if (!$this->service->isLoggedIn()) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';

        return $this->service->changePassword(
            $_SESSION['user_id'],
            $currentPassword,
            $newPassword,
            $newPasswordConfirm
        );
    }

    /**
     * 프로필 사진 변경 (API)
     * POST /api.php?method=user.updateProfilePhoto
     */
    public function updateProfilePhoto(): array
    {
        if (!$this->service->isLoggedIn()) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $uploadId = (int)($_POST['upload_id'] ?? 0);
        return $this->service->updateProfilePhoto($_SESSION['user_id'], $uploadId);
    }

    /**
     * 현재 사용자 정보 조회 (API)
     * GET /api.php?method=user.me
     */
    public function me(): array
    {
        if (!$this->service->isLoggedIn()) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $user = $this->service->getCurrentUser();
        if ($user === null) {
            return ['success' => false, 'message' => '사용자를 찾을 수 없습니다.'];
        }

        return ['success' => true, 'user' => $user->toArray()];
    }
}
