<?php

namespace lib\upload;

/**
 * 파일 업로드 관련 HTTP 요청을 처리하는 컨트롤러
 */
class UploadController
{
    private UploadService $service;

    public function __construct()
    {
        $this->service = new UploadService();
    }

    /**
     * 파일 업로드 (API)
     * POST /api.php?method=upload.upload
     * FormData: file (파일)
     */
    public function upload(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'message' => '업로드할 파일을 선택해주세요.'];
        }

        return $this->service->upload($_FILES['file'], $_SESSION['user_id']);
    }

    /**
     * 파일 삭제 (API)
     * POST /api.php?method=upload.delete
     */
    public function delete(): array
    {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => '로그인이 필요합니다.'];
        }

        $id = (int)($_POST['id'] ?? 0);
        return $this->service->delete($id, $_SESSION['user_id']);
    }

    /**
     * 파일 정보 조회 (API)
     * GET /api.php?method=upload.view&id=1
     */
    public function view(): array
    {
        $id = (int)($_GET['id'] ?? 0);
        $upload = $this->service->getUpload($id);

        if ($upload === null) {
            return ['success' => false, 'message' => '파일을 찾을 수 없습니다.'];
        }

        return ['success' => true, 'upload' => $upload->toArray()];
    }
}
