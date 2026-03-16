<?php
$pageTitle = '404 - 페이지를 찾을 수 없습니다';
require __DIR__ . '/layout/header.php';
?>

<div class="card" style="text-align: center; padding: 80px 32px;">
    <div style="font-size: 6em; font-weight: 800; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1;">404</div>
    <p style="margin-top: 20px; color: var(--gray-500); font-size: 1.1em; font-weight: 500;">요청하신 페이지를 찾을 수 없습니다.</p>
    <p style="margin-top: 8px; color: var(--gray-400); font-size: 0.9em;">주소가 올바른지 확인해 주세요.</p>
    <a href="/" class="btn btn-primary" style="margin-top: 28px;">홈으로 돌아가기</a>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
