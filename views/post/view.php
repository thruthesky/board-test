<?php
$service = new \lib\post\PostService();
$id = (int)($_GET['id'] ?? 0);

// 댓글 관련 POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $commentService = new \lib\comment\CommentService();

    if ($_POST['action'] === 'comment_create' && isset($_SESSION['user_id'])) {
        $postId = (int)($_POST['post_id'] ?? 0);
        $content = $_POST['content'] ?? '';
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $commentService->create($postId, $_SESSION['user_id'], $content, $parentId);
        header('Location: /post/view?id=' . $id . '#comment-section');
        exit;
    }

    if ($_POST['action'] === 'comment_delete' && isset($_SESSION['user_id'])) {
        $commentId = (int)($_POST['comment_id'] ?? 0);
        $commentService->delete($commentId, $_SESSION['user_id']);
        header('Location: /post/view?id=' . $id . '#comment-section');
        exit;
    }
}

$post = $service->getPost($id);

if ($post === null) {
    http_response_code(404);
    $pageTitle = '게시글을 찾을 수 없습니다';
    require __DIR__ . '/../layout/header.php';
    echo '<div class="card"><p>게시글을 찾을 수 없습니다.</p><a href="/post/list" class="btn btn-secondary">목록으로</a></div>';
    require __DIR__ . '/../layout/footer.php';
    exit;
}

$pageTitle = htmlspecialchars($post->title);
$isAuthor = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $post->user_id;

require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <h2><?= htmlspecialchars($post->title) ?></h2>
    <div style="color: #888; margin: 10px 0 20px; font-size: 0.9em;">
        작성자: <?= htmlspecialchars($post->author_name ?? '') ?> |
        작성일: <?= date('Y-m-d H:i', strtotime($post->created_at)) ?>
        <?php if ($post->updated_at !== $post->created_at): ?>
            | 수정일: <?= date('Y-m-d H:i', strtotime($post->updated_at)) ?>
        <?php endif; ?>
    </div>
    <div style="min-height: 200px; white-space: pre-wrap; line-height: 1.8;"><?= htmlspecialchars($post->content) ?></div>

    <div style="margin-top: 30px; display: flex; justify-content: space-between;">
        <a href="/post/list" class="btn btn-secondary">목록으로</a>
        <?php if ($isAuthor): ?>
            <div>
                <a href="/post/edit?id=<?= $post->id ?>" class="btn btn-primary">수정</a>
                <form method="POST" action="/post/delete" style="display: inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                    <input type="hidden" name="id" value="<?= $post->id ?>">
                    <button type="submit" class="btn btn-danger">삭제</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// 댓글 목록 조회
$commentService = new \lib\comment\CommentService();
$comments = $commentService->getCommentsByPostId($post->id);
$commentCount = $commentService->getCommentCount($post->id);
?>

<div class="card" style="margin-top: 24px;">
    <h3 style="margin-bottom: 20px; font-size: 1.1em;">댓글 (<?= $commentCount ?>)</h3>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- 댓글 작성 폼 -->
        <form method="POST" action="/post/view?id=<?= $post->id ?>" style="margin-bottom: 24px;">
            <input type="hidden" name="action" value="comment_create">
            <input type="hidden" name="post_id" value="<?= $post->id ?>">
            <div class="form-group" style="margin-bottom: 10px;">
                <textarea name="content" placeholder="댓글을 입력하세요..." style="min-height: 80px;" required></textarea>
            </div>
            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary">댓글 작성</button>
            </div>
        </form>
    <?php else: ?>
        <p style="color: var(--gray-400); margin-bottom: 20px; padding: 16px; background: var(--gray-50); border-radius: var(--radius-sm); text-align: center;">
            댓글을 작성하려면 <a href="/user/login" style="color: var(--primary); text-decoration: underline;">로그인</a>해주세요.
        </p>
    <?php endif; ?>

    <?php if (empty($comments)): ?>
        <p style="text-align: center; color: var(--gray-400); padding: 32px 0;">아직 댓글이 없습니다.</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <!-- 댓글 -->
            <div class="comment" style="padding: 16px 0; border-top: 1px solid var(--gray-100);" id="comment-<?= $comment->id ?>">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div>
                        <strong style="font-size: 0.9em;"><?= htmlspecialchars($comment->author_name ?? '') ?></strong>
                        <span style="color: var(--gray-400); font-size: 0.8em; margin-left: 8px;"><?= date('Y-m-d H:i', strtotime($comment->created_at)) ?></span>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button onclick="toggleReplyForm(<?= $comment->id ?>)" style="background: none; border: none; color: var(--primary); cursor: pointer; font-size: 0.85em; font-family: inherit;">답글</button>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment->user_id): ?>
                            <form method="POST" action="/post/view?id=<?= $post->id ?>" style="display: inline;">
                                <input type="hidden" name="action" value="comment_delete">
                                <input type="hidden" name="comment_id" value="<?= $comment->id ?>">
                                <button type="submit" onclick="return confirm('댓글을 삭제하시겠습니까?')" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 0.85em; font-family: inherit;">삭제</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="white-space: pre-wrap; line-height: 1.6; font-size: 0.95em;"><?= htmlspecialchars($comment->content) ?></div>

                <!-- 대댓글 작성 폼 (숨김) -->
                <div id="reply-form-<?= $comment->id ?>" style="display: none; margin-top: 12px;">
                    <form method="POST" action="/post/view?id=<?= $post->id ?>">
                        <input type="hidden" name="action" value="comment_create">
                        <input type="hidden" name="post_id" value="<?= $post->id ?>">
                        <input type="hidden" name="parent_id" value="<?= $comment->id ?>">
                        <div class="form-group" style="margin-bottom: 8px;">
                            <textarea name="content" placeholder="답글을 입력하세요..." style="min-height: 60px;" required></textarea>
                        </div>
                        <div style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                            <button type="button" onclick="toggleReplyForm(<?= $comment->id ?>)" class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.85em;">취소</button>
                            <button type="submit" class="btn btn-primary" style="padding: 6px 14px; font-size: 0.85em;">답글 작성</button>
                        </div>
                    </form>
                </div>

                <!-- 대댓글 목록 -->
                <?php if (!empty($comment->children)): ?>
                    <?php foreach ($comment->children as $child): ?>
                        <div class="reply" style="margin-top: 12px; margin-left: 24px; padding: 12px 16px; background: var(--gray-50); border-radius: var(--radius-sm); border-left: 3px solid var(--primary-light);" id="comment-<?= $child->id ?>">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <div>
                                    <strong style="font-size: 0.85em;"><?= htmlspecialchars($child->author_name ?? '') ?></strong>
                                    <span style="color: var(--gray-400); font-size: 0.78em; margin-left: 8px;"><?= date('Y-m-d H:i', strtotime($child->created_at)) ?></span>
                                </div>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $child->user_id): ?>
                                    <form method="POST" action="/post/view?id=<?= $post->id ?>" style="display: inline;">
                                        <input type="hidden" name="action" value="comment_delete">
                                        <input type="hidden" name="comment_id" value="<?= $child->id ?>">
                                        <button type="submit" onclick="return confirm('답글을 삭제하시겠습니까?')" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 0.8em; font-family: inherit;">삭제</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div style="white-space: pre-wrap; line-height: 1.6; font-size: 0.9em;"><?= htmlspecialchars($child->content) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function toggleReplyForm(commentId) {
    var form = document.getElementById('reply-form-' + commentId);
    if (form.style.display === 'none') {
        // 다른 열린 답글 폼 모두 닫기
        document.querySelectorAll('[id^="reply-form-"]').forEach(function(el) {
            el.style.display = 'none';
        });
        form.style.display = 'block';
        form.querySelector('textarea').focus();
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
