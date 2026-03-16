<?php

use PHPUnit\Framework\TestCase;
use lib\upload\UploadService;
use lib\upload\UploadRepository;
use lib\user\UserService;
use lib\user\UserRepository;

/**
 * UploadService 단위 테스트
 */
class UploadServiceTest extends TestCase
{
    private UploadService $uploadService;
    private UserService $userService;
    private int $userId;
    private string $testUploadDir;

    protected function setUp(): void
    {
        // 매 테스트마다 인메모리 DB 재생성
        \Utils\Db::createInMemory();

        $this->uploadService = new UploadService();
        $this->userService = new UserService();

        // 테스트용 사용자 생성
        $result = $this->userService->register('테스트유저', 'upload-test@example.com', 'password123', 'password123');
        $this->userId = $result['user_id'];

        // 테스트용 업로드 디렉토리
        $this->testUploadDir = dirname(__DIR__) . '/uploads/' . $this->userId;
    }

    protected function tearDown(): void
    {
        // 테스트 업로드 파일 정리
        if (is_dir($this->testUploadDir)) {
            $files = glob($this->testUploadDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->testUploadDir);
        }
        \Utils\Db::resetInstance();
    }

    /**
     * 이미지 파일 업로드 테스트
     */
    public function testUploadImageFile(): void
    {
        // 테스트용 이미지 파일 생성
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_');
        // 최소한의 PNG 파일 생성
        $img = imagecreatetruecolor(10, 10);
        imagepng($img, $tmpFile);
        imagedestroy($img);

        $result = $this->uploadService->uploadFromPath($tmpFile, 'test-image.png', $this->userId);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('upload', $result);
        $this->assertEquals('test-image.png', $result['upload']['original_name']);
        $this->assertEquals('image/png', $result['upload']['mime_type']);
        $this->assertStringContainsString('uploads/' . $this->userId . '/', $result['upload']['path']);

        unlink($tmpFile);
    }

    /**
     * 업로드된 파일 조회 테스트
     */
    public function testGetUpload(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_');
        $img = imagecreatetruecolor(10, 10);
        imagepng($img, $tmpFile);
        imagedestroy($img);

        $uploadResult = $this->uploadService->uploadFromPath($tmpFile, 'fetch-test.png', $this->userId);
        $uploadId = $uploadResult['upload']['id'];

        $upload = $this->uploadService->getUpload($uploadId);

        $this->assertNotNull($upload);
        $this->assertEquals('fetch-test.png', $upload->original_name);
        $this->assertEquals($this->userId, $upload->user_id);

        unlink($tmpFile);
    }

    /**
     * 파일 삭제 테스트
     */
    public function testDeleteUpload(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_');
        $img = imagecreatetruecolor(10, 10);
        imagepng($img, $tmpFile);
        imagedestroy($img);

        $uploadResult = $this->uploadService->uploadFromPath($tmpFile, 'delete-test.png', $this->userId);
        $uploadId = $uploadResult['upload']['id'];

        // 파일 존재 확인
        $this->assertNotNull($this->uploadService->getUpload($uploadId));

        // 삭제
        $deleteResult = $this->uploadService->delete($uploadId, $this->userId);
        $this->assertTrue($deleteResult['success']);

        // 삭제 확인
        $this->assertNull($this->uploadService->getUpload($uploadId));

        unlink($tmpFile);
    }

    /**
     * 다른 사용자의 파일 삭제 권한 테스트
     */
    public function testCannotDeleteOtherUserFile(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_');
        $img = imagecreatetruecolor(10, 10);
        imagepng($img, $tmpFile);
        imagedestroy($img);

        $uploadResult = $this->uploadService->uploadFromPath($tmpFile, 'auth-test.png', $this->userId);
        $uploadId = $uploadResult['upload']['id'];

        // 다른 사용자 생성
        $otherResult = $this->userService->register('다른유저', 'other@example.com', 'password123', 'password123');
        $otherUserId = $otherResult['user_id'];

        // 다른 사용자로 삭제 시도
        $deleteResult = $this->uploadService->delete($uploadId, $otherUserId);
        $this->assertFalse($deleteResult['success']);
        $this->assertStringContainsString('권한', $deleteResult['message']);

        unlink($tmpFile);
    }

    /**
     * 프로필 사진 업로드 및 적용 테스트
     */
    public function testProfilePhotoUploadAndApply(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_avatar_');
        $img = imagecreatetruecolor(100, 100);
        imagepng($img, $tmpFile);
        imagedestroy($img);

        // 파일 업로드
        $uploadResult = $this->uploadService->uploadFromPath($tmpFile, 'avatar.png', $this->userId);
        $this->assertTrue($uploadResult['success']);
        $uploadId = $uploadResult['upload']['id'];

        // 프로필 사진으로 설정
        $_SESSION['user_id'] = $this->userId;
        $profileResult = $this->userService->updateProfilePhoto($this->userId, $uploadId);
        $this->assertTrue($profileResult['success']);
        $this->assertArrayHasKey('profile_photo_url', $profileResult);

        // 사용자 정보에 프로필 사진이 반영되었는지 확인
        $user = (new UserRepository())->findById($this->userId);
        $this->assertEquals($uploadId, $user->profile_photo_id);
        $this->assertNotNull($user->getProfilePhotoUrl());

        unlink($tmpFile);
    }

    /**
     * 이미지가 아닌 파일을 프로필 사진으로 설정 시 실패 테스트
     */
    public function testCannotSetNonImageAsProfilePhoto(): void
    {
        // 텍스트 파일 생성
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_txt_');
        file_put_contents($tmpFile, 'Hello, World!');

        $uploadResult = $this->uploadService->uploadFromPath($tmpFile, 'test.txt', $this->userId);
        $this->assertTrue($uploadResult['success']);
        $uploadId = $uploadResult['upload']['id'];

        // 텍스트 파일을 프로필로 설정 시도
        $profileResult = $this->userService->updateProfilePhoto($this->userId, $uploadId);
        $this->assertFalse($profileResult['success']);
        $this->assertStringContainsString('이미지', $profileResult['message']);

        unlink($tmpFile);
    }

    /**
     * 사용자 목록에서 프로필 사진 경로 확인 테스트
     */
    public function testUserProfilePhotoInPostList(): void
    {
        // 프로필 사진 업로드 및 설정
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_avatar_');
        $img = imagecreatetruecolor(50, 50);
        imagepng($img, $tmpFile);
        imagedestroy($img);

        $uploadResult = $this->uploadService->uploadFromPath($tmpFile, 'post-avatar.png', $this->userId);
        $_SESSION['user_id'] = $this->userId;
        $this->userService->updateProfilePhoto($this->userId, $uploadResult['upload']['id']);

        // 게시글 작성
        $postService = new \lib\post\PostService();
        $postResult = $postService->create($this->userId, '아바타 테스트 글', '프로필 사진이 표시되는지 테스트');
        $this->assertTrue($postResult['success']);

        // 게시글 조회 시 작성자 아바타 경로 확인
        $post = $postService->getPost($postResult['post_id']);
        $this->assertNotNull($post);
        $this->assertNotNull($post->getAuthorPhotoUrl());
        $this->assertStringContainsString('uploads/', $post->getAuthorPhotoUrl());

        unlink($tmpFile);
    }
}
