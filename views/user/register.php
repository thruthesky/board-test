<?php
$pageTitle = '회원가입';
$error = '';
$success = '';

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service = new \lib\user\UserService();
    $result = $service->register(
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['password_confirm'] ?? ''
    );

    if ($result['success']) {
        // 가입 후 자동 로그인
        $service->login($_POST['email'], $_POST['password']);
        header('Location: /post/list');
        exit;
    } else {
        $error = $result['message'];
    }
}

require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <h2>회원가입</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/user/register">
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" required minlength="4">
        </div>
        <div class="form-group">
            <label for="password_confirm">비밀번호 확인</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="4">
        </div>
        <button type="submit" class="btn btn-primary">가입하기</button>
        <a href="/user/login" class="btn btn-secondary">로그인으로 이동</a>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
