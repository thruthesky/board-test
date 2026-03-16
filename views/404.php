<?php
$pageTitle = '404 - 페이지를 찾을 수 없습니다';
require __DIR__ . '/layout/header.php';
?>

<div class="card" style="text-align: center; padding: 60px 32px;">
    <div style="font-size: 4em; font-weight: 800; color: var(--gray-200); line-height: 1;">404</div>
    <p style="margin-top: 16px; color: var(--gray-500); font-size: 1.05em;">요청하신 페이지를 찾을 수 없습니다.</p>
    <a href="/" class="btn btn-primary" style="margin-top: 24px;">홈으로 돌아가기</a>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
