<?php

/**
 * API 엔드포인트 라우터
 * 사용법: /api.php?method=도메인.액션
 * 예: /api.php?method=user.register, /api.php?method=post.list
 */

// PSR-4 수동 오토로더
spl_autoload_register(function (string $class) {
    // 네임스페이스 구분자를 디렉토리 구분자로 변환
    $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// 세션 시작
session_start();

header('Content-Type: application/json; charset=utf-8');

// method 파라미터 확인
if (!isset($_GET['method'])) {
    http_response_code(400);
    echo json_encode(['error' => 'method 파라미터가 필요합니다.']);
    exit;
}

$parts = explode('.', $_GET['method']);
if (count($parts) !== 2) {
    http_response_code(400);
    echo json_encode(['error' => '잘못된 메서드 형식입니다. (예: user.register, post.list)']);
    exit;
}

[$domain, $action] = $parts;

try {
    switch ($domain) {
        case 'user':
            $controller = new \lib\user\UserController();
            break;
        case 'post':
            $controller = new \lib\post\PostController();
            break;
        case 'comment':
            $controller = new \lib\comment\CommentController();
            break;
        case 'upload':
            $controller = new \lib\upload\UploadController();
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => '알 수 없는 도메인입니다.']);
            exit;
    }

    if (!method_exists($controller, $action)) {
        http_response_code(404);
        echo json_encode(['error' => '알 수 없는 액션입니다.']);
        exit;
    }

    $result = $controller->$action();
    echo json_encode($result);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
