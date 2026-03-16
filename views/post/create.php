<?php
$pageTitle = '글쓰기';
$error = '';

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    header('Location: /user/login');
    exit;
}

$category = $_GET['category'] ?? 'discussion';

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service = new \lib\post\PostService();
    $result = $service->create(
        $_SESSION['user_id'],
        $_POST['title'] ?? '',
        $_POST['content'] ?? '',
        $_POST['category'] ?? 'discussion',
        $_POST['attachment_ids'] ?? []
    );

    if ($result['success']) {
        header('Location: /post/view?id=' . $result['post_id']);
        exit;
    } else {
        $error = $result['message'];
    }
}

require __DIR__ . '/../layout/header.php';
?>

<div class="card" id="create-post-app">
    <h2>글쓰기</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/post/create">
        <div class="form-group">
            <label for="category">카테고리</label>
            <select id="category" name="category" style="width:100%; padding:12px 16px; border:1.5px solid var(--gray-200); border-radius:var(--radius-sm); font-size:0.95em; font-family:inherit; color:var(--gray-800); background:var(--gray-50); outline:none;">
                <?php
                $categoryNames = ['discussion' => '자유토론', 'qna' => '질문답변', 'news' => '뉴스'];
                $selectedCategory = $_POST['category'] ?? $category;
                foreach ($categoryNames as $key => $name): ?>
                    <option value="<?= $key ?>" <?= $selectedCategory === $key ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="title">제목</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required maxlength="200">
        </div>
        <div class="form-group">
            <label for="content">내용</label>
            <textarea id="content" name="content" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
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

        <button type="submit" class="btn btn-primary">작성하기</button>
        <a href="/post/list" class="btn btn-secondary">취소</a>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>

<script>
Vue.createApp({
    data() {
        return {
            files: [],
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
}).mount('#create-post-app');
</script>
