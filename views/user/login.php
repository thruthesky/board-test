<?php
$pageTitle = '로그인';
$error = '';

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service = new \lib\user\UserService();
    $result = $service->login(
        $_POST['email'] ?? '',
        $_POST['password'] ?? ''
    );

    if ($result['success']) {
        header('Location: /post/list');
        exit;
    } else {
        $error = $result['message'];
    }
}

require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <h2>로그인</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/user/login">
        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">로그인</button>
        <a href="/user/register" class="btn btn-secondary">회원가입으로 이동</a>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
