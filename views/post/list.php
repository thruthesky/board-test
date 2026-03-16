<?php
$service = new \lib\post\PostService();
$page = max(1, (int)($_GET['page'] ?? 1));
$category = $_GET['category'] ?? '';

$categoryNames = [
    'discussion' => '자유토론',
    'qna' => '질문답변',
    'news' => '뉴스',
];

if ($category && in_array($category, \lib\post\PostService::CATEGORIES, true)) {
    $data = $service->getListByCategory($category, $page);
    $pageTitle = $categoryNames[$category] ?? '게시글 목록';
} else {
    $data = $service->getList($page);
    $pageTitle = '게시글 목록';
    $category = '';
}

require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;" class="mb-20">
        <h2>게시글 목록</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/post/create" class="btn btn-primary">글쓰기</a>
        <?php endif; ?>
    </div>

    <?php if (empty($data['posts'])): ?>
        <p style="text-align: center; padding: 40px 0; color: #999;">게시글이 없습니다.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">번호</th>
                    <th>제목</th>
                    <th style="width: 100px;">작성자</th>
                    <th style="width: 120px;">작성일</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['posts'] as $post): ?>
                    <tr>
                        <td><?= $post->id ?></td>
                        <td><a href="/post/view?id=<?= $post->id ?>" style="color: #333; text-decoration: none;"><?= htmlspecialchars($post->title) ?></a></td>
                        <td>
                            <span class="author-info">
                                <span class="avatar avatar-sm">
                                    <?php if ($post->getAuthorPhotoUrl()): ?>
                                        <img src="<?= htmlspecialchars($post->getAuthorPhotoUrl()) ?>" alt="">
                                    <?php else: ?>
                                        <?= mb_substr($post->author_name ?? '', 0, 1) ?>
                                    <?php endif; ?>
                                </span>
                                <?= htmlspecialchars($post->author_name ?? '') ?>
                            </span>
                        </td>
                        <td><?= date('Y-m-d', strtotime($post->created_at)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($data['total_pages'] > 1): ?>
            <?php
            $baseUrl = '/post/list?' . ($category ? 'category=' . urlencode($category) . '&' : '');
            $cur = $data['current_page'];
            $total = $data['total_pages'];
            $maxVisible = 7;
            $startPage = max(1, $cur - (int)floor($maxVisible / 2));
            $endPage = min($total, $startPage + $maxVisible - 1);
            $startPage = max(1, $endPage - $maxVisible + 1);
            ?>
            <div class="pagination">
                <?php if ($cur > 1): ?>
                    <a href="<?= $baseUrl ?>page=1" title="처음">&laquo;</a>
                    <a href="<?= $baseUrl ?>page=<?= $cur - 1 ?>" title="이전">&lsaquo;</a>
                <?php endif; ?>

                <?php if ($startPage > 1): ?>
                    <span class="dots">&hellip;</span>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i === $cur): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($endPage < $total): ?>
                    <span class="dots">&hellip;</span>
                <?php endif; ?>

                <?php if ($cur < $total): ?>
                    <a href="<?= $baseUrl ?>page=<?= $cur + 1 ?>" title="다음">&rsaquo;</a>
                    <a href="<?= $baseUrl ?>page=<?= $total ?>" title="마지막">&raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
