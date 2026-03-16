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

// 기존 첨부파일 조회
$existingAttachments = $service->getAttachments($id);

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $service->update(
        $id,
        $_SESSION['user_id'],
        $_POST['title'] ?? '',
        $_POST['content'] ?? '',
        $_POST['attachment_ids'] ?? []
    );

    if ($result['success']) {
        header('Location: /post/view?id=' . $id);
        exit;
    } else {
        $error = $result['message'];
        $post->title = $_POST['title'] ?? $post->title;
        $post->content = $_POST['content'] ?? $post->content;
    }
}

require __DIR__ . '/../layout/header.php';
?>

<div class="card" id="edit-post-app">
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

        <!-- 파일 첨부 (Vue.js) -->
        <div class="form-group">
            <label>파일 첨부</label>
            <div class="file-upload-area"
                 @click="$refs.fileInput.click()"
                 @dragover.prevent="dragover=true"
                 @dragleave="dragover=false"
                 @drop.prevent="onDrop"
                 :class="{dragover}">
                <input type="file" ref="fileInput" multiple accept="image/*,video/*,.pdf,.txt,.zip" @change="onFileSelect">
                <p style="color: var(--gray-500); font-size: 0.9em;">클릭하거나 파일을 드래그하세요 (이미지, 동영상, PDF 등)</p>
            </div>
            <div class="file-preview" v-if="files.length > 0">
                <div class="file-preview-item" v-for="(f, i) in files" :key="f.id">
                    <img v-if="f.isImage" :src="f.url" :alt="f.name">
                    <div v-else style="display:flex; align-items:center; justify-content:center; width:100%; height:100%; background:var(--gray-100); font-size:0.7em; color:var(--gray-500); padding:8px; text-align:center; word-break:break-all;">{{ f.name }}</div>
                    <span class="file-name">{{ f.name }}</span>
                    <button type="button" class="remove-btn" @click.stop="removeFile(i)">&times;</button>
                </div>
            </div>
            <!-- 업로드된 파일 ID를 hidden으로 전달 -->
            <input v-for="f in files" :key="f.id" type="hidden" name="attachment_ids[]" :value="f.id">
        </div>

        <button type="submit" class="btn btn-primary">수정하기</button>
        <a href="/post/view?id=<?= $post->id ?>" class="btn btn-secondary">취소</a>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>

<script>
Vue.createApp({
    data() {
        return {
            files: <?= json_encode(array_map(function($att) {
                return [
                    'id' => $att->id,
                    'name' => $att->original_name,
                    'url' => $att->getUrl(),
                    'isImage' => $att->isImage(),
                ];
            }, $existingAttachments)) ?>,
            dragover: false,
        };
    },
    methods: {
        async onFileSelect(e) {
            for (const file of e.target.files) {
                await this.handleUpload(file);
            }
            e.target.value = '';
        },
        async onDrop(e) {
            this.dragover = false;
            for (const file of e.dataTransfer.files) {
                await this.handleUpload(file);
            }
        },
        async handleUpload(file) {
            try {
                const result = await uploadFile(file);
                if (result.success) {
                    this.files.push({
                        id: result.upload.id,
                        name: result.upload.original_name,
                        url: result.upload.url,
                        isImage: result.upload.mime_type.startsWith('image/'),
                    });
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('파일 업로드 중 오류가 발생했습니다.');
            }
        },
        removeFile(index) {
            this.files.splice(index, 1);
        }
    }
}).mount('#edit-post-app');
</script>
