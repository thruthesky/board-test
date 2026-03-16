<?php
// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    header('Location: /user/login');
    exit;
}

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /post/list');
    exit;
}

$service = new \lib\post\PostService();
$id = (int)($_POST['id'] ?? 0);
$result = $service->delete($id, $_SESSION['user_id']);

header('Location: /post/list');
exit;
