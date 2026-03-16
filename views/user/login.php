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

</div><!-- 기본 container 닫기 -->

<div style="max-width: 440px; margin: 0 auto; padding: 0 24px;">
    <div class="card" style="padding: 40px 36px;">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); border-radius: 16px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            </div>
            <h2 style="margin-bottom: 4px;">로그인</h2>
            <p style="color: var(--gray-400); font-size: 0.9em;">계정에 로그인하세요</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/user/login">
            <div class="form-group">
                <label for="email">이메일</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="name@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" id="password" name="password" placeholder="비밀번호를 입력하세요" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 4px;">로그인</button>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 0.9em; color: var(--gray-500);">
            계정이 없으신가요? <a href="/user/register" style="color: var(--primary); font-weight: 600; text-decoration: none;">회원가입</a>
        </p>
    </div>
</div>

<div class="container"><!-- footer 위해 다시 열기 -->

<?php require __DIR__ . '/../layout/footer.php'; ?>
