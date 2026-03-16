<?php
$pageTitle = '홈 - 게시판';
$service = new \lib\post\PostService();

// 카테고리 설정
$categories = [
    'discussion' => [
        'label' => '자유토론',
        'badge' => 'cat-badge-indigo',
        'image' => 'https://picsum.photos/seed/discuss-board/600/300',
    ],
    'qna' => [
        'label' => '질문답변',
        'badge' => 'cat-badge-emerald',
        'image' => 'https://picsum.photos/seed/qna-board/600/300',
    ],
    'news' => [
        'label' => '뉴스',
        'badge' => 'cat-badge-amber',
        'image' => 'https://picsum.photos/seed/news-board/600/300',
    ],
];

// 카테고리별 최근 5개 글 조회
$categoryData = [];
foreach (array_keys($categories) as $cat) {
    $categoryData[$cat] = $service->getListByCategory($cat, 1, 5);
}

require __DIR__ . '/layout/header.php';
?>

</div><!-- 기본 container 닫기 -->

<div class="wide-container">
    <!-- 히어로 섹션 -->
    <div class="hero">
        <h1>커뮤니티에 오신 것을 환영합니다</h1>
        <p>자유롭게 글을 쓰고, 질문하고, 소식을 나눠보세요.</p>
        <div class="hero-actions">
            <a href="/post/list" class="btn-hero btn-hero-primary">전체 글 보기</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/user/register" class="btn-hero btn-hero-outline">회원가입</a>
            <?php else: ?>
                <a href="/post/create" class="btn-hero btn-hero-outline">새 글 작성</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 카테고리별 최근 글 그리드 -->
    <div class="home-grid">
        <?php foreach ($categories as $catKey => $catInfo): ?>
            <div class="card">
                <img src="<?= $catInfo['image'] ?>" alt="<?= $catInfo['label'] ?>" class="cat-cover" loading="lazy">
                <div class="cat-body">
                    <div class="cat-header">
                        <h3>
                            <?= $catInfo['label'] ?>
                            <span class="cat-badge <?= $catInfo['badge'] ?>"><?= $categoryData[$catKey]['total_count'] ?></span>
                        </h3>
                        <a href="/post/list?category=<?= $catKey ?>">더보기 &rarr;</a>
                    </div>

                    <?php if (empty($categoryData[$catKey]['posts'])): ?>
                        <div class="cat-empty">아직 게시글이 없습니다.</div>
                    <?php else: ?>
                        <ul class="cat-list">
                            <?php foreach ($categoryData[$catKey]['posts'] as $post): ?>
                                <li>
                                    <a href="/post/view?id=<?= $post->id ?>"><?= htmlspecialchars($post->title) ?></a>
                                    <span class="meta"><?= date('m.d', strtotime($post->created_at)) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="container"><!-- footer 위해 다시 열기 -->

<?php require __DIR__ . '/layout/footer.php'; ?>
