    </div>

    <footer>
        <div class="container" style="max-width: 1100px; margin: 48px auto 0; padding: 32px 24px; text-align: center; border-top: 1px solid var(--gray-200);">
            <p style="color: var(--gray-400); font-size: 0.85em; font-weight: 400;">
                &copy; <?= date('Y') ?> 게시판 &mdash; PHP + SQLite로 만든 커뮤니티
            </p>
        </div>
    </footer>

    <!-- Vue.js 3 CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script>
    // 전역 상태 관리 (Vue.js reactive)
    const AppState = Vue.reactive({
        user: {
            id: <?= json_encode($_SESSION['user_id'] ?? null) ?>,
            name: <?= json_encode($_SESSION['user_name'] ?? null) ?>,
            profilePhotoUrl: <?= json_encode($_SESSION['user_profile_photo_url'] ?? null) ?>,
        }
    });

    /**
     * 파일 업로드 API 호출
     */
    async function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        const res = await fetch('/api.php?method=upload.upload', { method: 'POST', body: formData });
        return res.json();
    }

    /**
     * 프로필 사진 변경 API
     */
    async function updateProfilePhoto(uploadId) {
        const formData = new FormData();
        formData.append('upload_id', uploadId);
        const res = await fetch('/api.php?method=user.updateProfilePhoto', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            AppState.user.profilePhotoUrl = data.profile_photo_url;
            const navAvatar = document.getElementById('nav-avatar');
            if (navAvatar) {
                navAvatar.innerHTML = '<img src="' + data.profile_photo_url + '" alt="">';
            }
        }
        return data;
    }
    </script>
</body>
</html>
