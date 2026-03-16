    </div>

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
     * @param {File} file
     * @returns {Promise<object>} { success, message, upload }
     */
    async function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        const res = await fetch('/api.php?method=upload.upload', { method: 'POST', body: formData });
        return res.json();
    }

    /**
     * 프로필 사진 변경 API
     * @param {number} uploadId
     */
    async function updateProfilePhoto(uploadId) {
        const formData = new FormData();
        formData.append('upload_id', uploadId);
        const res = await fetch('/api.php?method=user.updateProfilePhoto', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            // 전역 상태 업데이트
            AppState.user.profilePhotoUrl = data.profile_photo_url;
            // 탑바 아바타 즉시 갱신
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
