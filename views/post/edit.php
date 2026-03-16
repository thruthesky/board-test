<?php
$pageTitle = '게시글 수정';
$error = '';

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    header('Location: /user/login');
    exit;
}

$service = new \lib\post\PostService();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$post = $service->getPost($id);

// 게시글 존재 및 권한 확인
if ($post === null) {
    header('Location: /post/list');
    exit;
}
if ($post->user_id !== $_SESSION['user_id']) {
    header('Location: /post/view?id=' . $id);
    exit;
}

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $service->update(
        $id,
        $_SESSION['user_id'],
        $_POST['title'] ?? '',
        $_POST['content'] ?? ''
    );

    if ($result['success']) {
        header('Location: /post/view?id=' . $id);
        exit;
    } else {
        $error = $result['message'];
        // 수정 실패 시 입력값 유지
        $post->title = $_POST['title'] ?? $post->title;
        $post->content = $_POST['content'] ?? $post->content;
    }
}

require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <h2>게시글 수정</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/post/edit?id=<?= $post->id ?>">
        <input type="hidden" name="id" value="<?= $post->id ?>">
        <div class="form-group">
            <label for="title">제목</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($post->title) ?>" required maxlength="200">
        </div>
        <div class="form-group">
            <label for="content">내용</label>
            <textarea id="content" name="content" required><?= htmlspecialchars($post->content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">수정하기</button>
        <a href="/post/view?id=<?= $post->id ?>" class="btn btn-secondary">취소</a>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
