<?php

namespace lib\upload;

/**
 * 파일 업로드 비즈니스 로직을 처리하는 서비스 클래스
 */
class UploadService
{
    private UploadRepository $repository;

    // 허용 MIME 타입
    private const ALLOWED_TYPES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'video/mp4', 'video/webm', 'video/quicktime',
        'application/pdf',
        'text/plain',
        'application/zip',
    ];

    // 최대 파일 크기 (10MB)
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;

    // 업로드 기본 디렉토리
    private string $uploadBaseDir;

    public function __construct()
    {
        $this->repository = new UploadRepository();
        $this->uploadBaseDir = dirname(__DIR__, 2) . '/uploads';
    }

    /**
     * 파일 업로드 처리
     * @param array $file $_FILES 배열의 개별 파일 정보
     * @param int $userId 업로드하는 사용자 ID
     * @return array ['success' => bool, 'message' => string, 'upload' => array|null]
     */
    public function upload(array $file, int $userId): array
    {
        // 업로드 에러 확인
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => '파일 업로드 중 오류가 발생했습니다. (코드: ' . $file['error'] . ')'];
        }

        // 파일 크기 확인
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return ['success' => false, 'message' => '파일 크기는 10MB 이하여야 합니다.'];
        }

        // MIME 타입 확인
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_TYPES, true)) {
            return ['success' => false, 'message' => '허용되지 않는 파일 형식입니다. (' . $mimeType . ')'];
        }

        // 저장 디렉토리 생성: uploads/<user_id>/
        $userDir = $this->uploadBaseDir . '/' . $userId;
        if (!is_dir($userDir)) {
            mkdir($userDir, 0755, true);
        }

        // 고유 파일명 생성
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $storedName = uniqid('file_', true) . ($extension ? '.' . $extension : '');
        $relativePath = 'uploads/' . $userId . '/' . $storedName;
        $absolutePath = $this->uploadBaseDir . '/' . $userId . '/' . $storedName;

        // 파일 이동
        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            return ['success' => false, 'message' => '파일 저장에 실패했습니다.'];
        }

        // DB에 레코드 저장
        $entity = new UploadEntity([
            'user_id' => $userId,
            'original_name' => $file['name'],
            'stored_name' => $storedName,
            'path' => $relativePath,
            'mime_type' => $mimeType,
            'file_size' => $file['size'],
        ]);

        $uploadId = $this->repository->create($entity);
        $entity->id = $uploadId;

        return [
            'success' => true,
            'message' => '파일이 업로드되었습니다.',
            'upload' => $entity->toArray(),
        ];
    }

    /**
     * 테스트용: 파일 경로로 직접 업로드 (move_uploaded_file 대신 copy 사용)
     */
    public function uploadFromPath(string $filePath, string $originalName, int $userId): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => '파일을 찾을 수 없습니다.'];
        }

        $fileSize = filesize($filePath);
        if ($fileSize > self::MAX_FILE_SIZE) {
            return ['success' => false, 'message' => '파일 크기는 10MB 이하여야 합니다.'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath);
        if (!in_array($mimeType, self::ALLOWED_TYPES, true)) {
            return ['success' => false, 'message' => '허용되지 않는 파일 형식입니다.'];
        }

        $userDir = $this->uploadBaseDir . '/' . $userId;
        if (!is_dir($userDir)) {
            mkdir($userDir, 0755, true);
        }

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $storedName = uniqid('file_', true) . ($extension ? '.' . $extension : '');
        $relativePath = 'uploads/' . $userId . '/' . $storedName;
        $absolutePath = $this->uploadBaseDir . '/' . $userId . '/' . $storedName;

        if (!copy($filePath, $absolutePath)) {
            return ['success' => false, 'message' => '파일 저장에 실패했습니다.'];
        }

        $entity = new UploadEntity([
            'user_id' => $userId,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'path' => $relativePath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        $uploadId = $this->repository->create($entity);
        $entity->id = $uploadId;

        return [
            'success' => true,
            'message' => '파일이 업로드되었습니다.',
            'upload' => $entity->toArray(),
        ];
    }

    /**
     * 업로드 파일 조회
     */
    public function getUpload(int $id): ?UploadEntity
    {
        return $this->repository->findById($id);
    }

    /**
     * 업로드 파일 삭제
     */
    public function delete(int $uploadId, int $userId): array
    {
        $upload = $this->repository->findById($uploadId);
        if ($upload === null) {
            return ['success' => false, 'message' => '파일을 찾을 수 없습니다.'];
        }

        if ($upload->user_id !== $userId) {
            return ['success' => false, 'message' => '삭제 권한이 없습니다.'];
        }

        // 실제 파일 삭제
        $absolutePath = dirname(__DIR__, 2) . '/' . $upload->path;
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        $this->repository->delete($uploadId);

        return ['success' => true, 'message' => '파일이 삭제되었습니다.'];
    }

    /**
     * 사용자의 업로드 파일 목록 조회
     */
    public function getUserUploads(int $userId): array
    {
        return $this->repository->findByUserId($userId);
    }
}
