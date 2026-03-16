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

</div><!-- 기본 container 닫기 -->

<div style="max-width: 440px; margin: 0 auto; padding: 0 24px;">
    <div class="card" style="padding: 40px 36px;">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); border-radius: 16px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            </div>
            <h2 style="margin-bottom: 4px;">회원가입</h2>
            <p style="color: var(--gray-400); font-size: 0.9em;">새 계정을 만드세요</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/user/register">
            <div class="form-group">
                <label for="name">이름</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="홍길동" required>
            </div>
            <div class="form-group">
                <label for="email">이메일</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="name@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" id="password" name="password" placeholder="4자 이상 입력" required minlength="4">
            </div>
            <div class="form-group">
                <label for="password_confirm">비밀번호 확인</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="비밀번호를 다시 입력" required minlength="4">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 4px;">가입하기</button>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 0.9em; color: var(--gray-500);">
            이미 계정이 있으신가요? <a href="/user/login" style="color: var(--primary); font-weight: 600; text-decoration: none;">로그인</a>
        </p>
    </div>
</div>

<div class="container"><!-- footer 위해 다시 열기 -->

<?php require __DIR__ . '/../layout/footer.php'; ?>
