<?php
$pageTitle = '회원정보 수정';
$error = '';
$success = '';

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    header('Location: /user/login');
    exit;
}

$service = new \lib\user\UserService();
$user = $service->getCurrentUser();

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update_info';

    if ($action === 'update_info') {
        $result = $service->update(
            $_SESSION['user_id'],
            $_POST['name'] ?? '',
            $_POST['email'] ?? ''
        );
        if ($result['success']) {
            $success = $result['message'];
            $user = $service->getCurrentUser();
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'change_password') {
        $result = $service->changePassword(
            $_SESSION['user_id'],
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? '',
            $_POST['new_password_confirm'] ?? ''
        );
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

require __DIR__ . '/../layout/header.php';
?>

<!-- 프로필 사진 (Vue.js) -->
<div class="card" id="avatar-app">
    <h2>프로필 사진</h2>
    <div style="display: flex; align-items: center; gap: 24px;">
        <span class="avatar avatar-xl" id="profile-avatar">
            <img v-if="photoUrl" :src="photoUrl" alt="프로필">
            <template v-else><?= mb_substr($user->name ?? '', 0, 1) ?></template>
        </span>
        <div>
            <div class="file-upload-area" @click="$refs.fileInput.click()" @dragover.prevent="dragover=true" @dragleave="dragover=false" @drop.prevent="onDrop" :class="{dragover}">
                <input type="file" ref="fileInput" accept="image/*" @change="onFileSelect">
                <p v-if="uploading" style="color: var(--primary);">업로드 중...</p>
                <p v-else style="color: var(--gray-500); font-size: 0.9em;">클릭하거나 이미지를 드래그하세요</p>
            </div>
            <p v-if="message" :style="{color: success ? 'var(--success-text)' : 'var(--error-text)', fontSize: '0.85em', marginTop: '8px'}">{{ message }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h2>회원정보 수정</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/user/edit">
        <input type="hidden" name="action" value="update_info">
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user->name ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">정보 수정</button>
    </form>
</div>

<div class="card">
    <h2>비밀번호 변경</h2>
    <form method="POST" action="/user/edit">
        <input type="hidden" name="action" value="change_password">
        <div class="form-group">
            <label for="current_password">현재 비밀번호</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">새 비밀번호</label>
            <input type="password" id="new_password" name="new_password" required minlength="4">
        </div>
        <div class="form-group">
            <label for="new_password_confirm">새 비밀번호 확인</label>
            <input type="password" id="new_password_confirm" name="new_password_confirm" required minlength="4">
        </div>
        <button type="submit" class="btn btn-primary">비밀번호 변경</button>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>

<script>
// 프로필 사진 업로드 Vue 앱
Vue.createApp({
    data() {
        return {
            photoUrl: <?= json_encode($user->getProfilePhotoUrl()) ?>,
            uploading: false,
            message: '',
            success: false,
            dragover: false,
        };
    },
    methods: {
        async onFileSelect(e) {
            const file = e.target.files[0];
            if (file) await this.handleUpload(file);
            e.target.value = '';
        },
        async onDrop(e) {
            this.dragover = false;
            const file = e.dataTransfer.files[0];
            if (file) await this.handleUpload(file);
        },
        async handleUpload(file) {
            if (!file.type.startsWith('image/')) {
                this.message = '이미지 파일만 업로드할 수 있습니다.';
                this.success = false;
                return;
            }
            this.uploading = true;
            this.message = '';
            try {
                // 1. 파일 업로드
                const uploadResult = await uploadFile(file);
                if (!uploadResult.success) {
                    this.message = uploadResult.message;
                    this.success = false;
                    return;
                }
                // 2. 프로필 사진으로 설정
                const profileResult = await updateProfilePhoto(uploadResult.upload.id);
                this.message = profileResult.message;
                this.success = profileResult.success;
                if (profileResult.success) {
                    this.photoUrl = profileResult.profile_photo_url;
                }
            } catch (err) {
                this.message = '업로드 중 오류가 발생했습니다.';
                this.success = false;
            } finally {
                this.uploading = false;
            }
        }
    }
}).mount('#avatar-app');
</script>
