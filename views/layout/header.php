<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '게시판') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --success: #10b981;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;
            --success-text: #065f46;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --error-text: #991b1b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }

        .container { max-width: 860px; margin: 0 auto; padding: 0 24px; }

        /* 네비게이션 */
        nav {
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-800) 100%);
            padding: 0;
            margin-bottom: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        nav .container {
            max-width: 1080px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }
        nav a {
            color: var(--gray-300);
            text-decoration: none;
            margin-left: 8px;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            font-size: 0.9em;
            font-weight: 500;
            transition: var(--transition);
        }
        nav a:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        nav .logo {
            font-size: 1.3em;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            margin-left: 0;
            padding: 0;
            letter-spacing: -0.02em;
        }
        nav .logo:hover { background: none; color: #fff; }
        nav .nav-links { display: flex; align-items: center; gap: 2px; }

        /* 카드 */
        .card {
            background: #fff;
            border-radius: var(--radius);
            padding: 32px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }
        .card:hover { box-shadow: var(--shadow-md); }
        .card h1, .card h2 {
            color: var(--gray-900);
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .card h1 { font-size: 1.75em; }
        .card h2 { font-size: 1.35em; margin-bottom: 24px; }

        /* 폼 */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.9em;
            color: var(--gray-700);
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 0.95em;
            font-family: inherit;
            color: var(--gray-800);
            background: var(--gray-50);
            transition: var(--transition);
            outline: none;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--gray-400);
        }
        .form-group textarea {
            min-height: 220px;
            resize: vertical;
            line-height: 1.8;
        }

        /* 버튼 */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 22px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            transition: var(--transition);
            gap: 6px;
            line-height: 1.5;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 1px 2px rgba(79,70,229,0.3);
        }
        .btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: 0 4px 8px rgba(79,70,229,0.3);
            transform: translateY(-1px);
        }
        .btn-danger {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 1px 2px rgba(239,68,68,0.3);
        }
        .btn-danger:hover {
            background: var(--danger-hover);
            box-shadow: 0 4px 8px rgba(239,68,68,0.3);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #fff;
            color: var(--gray-600);
            border: 1.5px solid var(--gray-300);
        }
        .btn-secondary:hover {
            background: var(--gray-50);
            color: var(--gray-800);
            border-color: var(--gray-400);
        }

        /* 알림 */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 24px;
            font-size: 0.9em;
            font-weight: 500;
            line-height: 1.6;
        }
        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }
        .alert-error {
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
        }

        /* 테이블 */
        table { width: 100%; border-collapse: collapse; }
        table th, table td {
            padding: 14px 16px;
            text-align: left;
        }
        table th {
            background: var(--gray-50);
            font-weight: 600;
            font-size: 0.85em;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 2px solid var(--gray-200);
        }
        table td {
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.95em;
        }
        table tbody tr {
            transition: var(--transition);
        }
        table tbody tr:hover {
            background: var(--primary-light);
        }
        table tbody tr:last-child td { border-bottom: none; }
        table td a {
            color: var(--gray-800);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        table td a:hover { color: var(--primary); }

        /* 페이지네이션 */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-100);
        }
        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--gray-600);
            font-size: 0.85em;
            font-weight: 500;
            transition: var(--transition);
        }
        .pagination a:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }
        .pagination .active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .pagination .dots {
            border: none;
            color: var(--gray-400);
            min-width: 24px;
            padding: 0;
            cursor: default;
        }

        /* 탭 */
        .tabs { display: flex; gap: 0; margin-bottom: 25px; border-bottom: 2px solid var(--gray-200); }
        .tab { padding: 12px 24px; text-decoration: none; color: var(--gray-500); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: var(--transition); font-size: 0.95em; }
        .tab:hover { color: var(--primary); }
        .tab.active { color: var(--primary); border-bottom-color: var(--primary); font-weight: 700; }

        /* 홈 히어로 */
        .hero {
            border-radius: var(--radius);
            padding: 56px 48px;
            color: #fff;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(79,70,229,0.92) 0%, rgba(124,58,237,0.88) 100%),
                        url('https://picsum.photos/seed/board-community/1400/500') center/cover no-repeat;
            min-height: 240px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hero h1 { font-size: 2em; font-weight: 700; margin-bottom: 10px; color: #fff; position: relative; z-index: 1; text-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .hero p { font-size: 1.05em; opacity: 0.9; position: relative; z-index: 1; text-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .hero .hero-actions { margin-top: 28px; display: flex; gap: 12px; flex-wrap: wrap; position: relative; z-index: 1; }
        .hero .btn-hero {
            display: inline-flex; align-items: center;
            padding: 12px 28px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.9em;
            text-decoration: none;
            transition: var(--transition);
        }
        .hero .btn-hero-primary { background: #fff; color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .hero .btn-hero-primary:hover { background: var(--gray-100); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .hero .btn-hero-outline { background: rgba(255,255,255,0.18); color: #fff; border: 1.5px solid rgba(255,255,255,0.4); backdrop-filter: blur(4px); }
        .hero .btn-hero-outline:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }

        /* 홈 그리드 */
        .home-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        .home-grid .card { margin-bottom: 0; padding: 0; overflow: hidden; }
        .home-grid .card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        /* 카테고리 카드 커버 이미지 */
        .cat-cover {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }
        .cat-body { padding: 24px; }

        /* 카테고리 카드 헤더 */
        .cat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 2px solid var(--gray-100);
        }
        .cat-header h3 {
            font-size: 1.05em;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
        }
        .cat-badge-indigo { background: var(--primary-light); color: var(--primary); }
        .cat-badge-emerald { background: #ecfdf5; color: #059669; }
        .cat-badge-amber { background: #fffbeb; color: #d97706; }
        .cat-header a {
            font-size: 0.85em;
            color: var(--gray-400);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        .cat-header a:hover { color: var(--primary); }

        /* 카테고리 게시글 목록 */
        .cat-list { list-style: none; }
        .cat-list li {
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .cat-list li:last-child { border-bottom: none; padding-bottom: 0; }
        .cat-list li:first-child { padding-top: 0; }
        .cat-list a {
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 500;
            transition: var(--transition);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }
        .cat-list a:hover { color: var(--primary); }
        .cat-list .meta {
            font-size: 0.78em;
            color: var(--gray-400);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .cat-empty {
            text-align: center;
            padding: 32px 0;
            color: var(--gray-400);
            font-size: 0.9em;
        }

        /* 아바타 */
        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75em;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden;
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-sm { width: 24px; height: 24px; font-size: 0.65em; }
        .avatar-lg { width: 80px; height: 80px; font-size: 2em; }
        .avatar-xl { width: 120px; height: 120px; font-size: 3em; }
        .nav-user { display: flex; align-items: center; gap: 8px; }
        .nav-user .avatar { border: 2px solid rgba(255,255,255,0.3); }

        /* 파일 업로드 */
        .file-upload-area {
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius);
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--gray-50);
        }
        .file-upload-area:hover, .file-upload-area.dragover {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .file-upload-area input[type="file"] { display: none; }
        .file-preview { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 12px; }
        .file-preview-item {
            position: relative;
            width: 100px; height: 100px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }
        .file-preview-item img { width: 100%; height: 100%; object-fit: cover; }
        .file-preview-item .remove-btn {
            position: absolute; top: 4px; right: 4px;
            width: 22px; height: 22px;
            background: rgba(0,0,0,0.6); color: #fff;
            border: none; border-radius: 50%;
            cursor: pointer; font-size: 12px;
            display: flex; align-items: center; justify-content: center;
        }
        .file-preview-item .file-name {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.5); color: #fff;
            font-size: 0.65em; padding: 3px 6px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* 작성자 정보 (아바타 + 이름) */
        .author-info {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* 유틸리티 */
        .text-right { text-align: right; }
        .mb-20 { margin-bottom: 20px; }
        .wide-container { max-width: 1080px; margin: 0 auto; padding: 0 24px; }

        /* 반응형 */
        @media (max-width: 1024px) {
            .home-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
            .wide-container { max-width: 100%; padding: 0 20px; }
            .cat-cover { height: 100px; }
        }
        @media (max-width: 768px) {
            .home-grid { grid-template-columns: 1fr; max-width: 500px; margin-left: auto; margin-right: auto; }
            .cat-cover { height: 140px; }
        }
        @media (max-width: 640px) {
            .container { padding: 0 16px; }
            nav .container { height: 56px; }
            nav a { padding: 6px 10px; font-size: 0.82em; margin-left: 4px; }
            nav .logo { font-size: 1.1em; }
            .card { padding: 24px 20px; border-radius: var(--radius-sm); }
            .home-grid .card { padding: 0; }
            table th, table td { padding: 10px 12px; }
            .hero { padding: 36px 24px; min-height: 180px; }
            .hero h1 { font-size: 1.4em; }
            .wide-container { padding: 0 16px; }
        }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <a href="/" class="logo">게시판</a>
            <div class="nav-links">
                <a href="/post/list?category=discussion">자유토론</a>
                <a href="/post/list?category=qna">질문답변</a>
                <a href="/post/list?category=news">뉴스</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/post/create">글쓰기</a>
                    <a href="/user/edit">내정보</a>
                    <a href="/user/logout" class="nav-user">
                        <span class="avatar avatar-sm" id="nav-avatar">
                            <?php if (!empty($_SESSION['user_profile_photo_url'])): ?>
                                <img src="<?= htmlspecialchars($_SESSION['user_profile_photo_url']) ?>" alt="">
                            <?php else: ?>
                                <?= mb_substr($_SESSION['user_name'], 0, 1) ?>
                            <?php endif; ?>
                        </span>
                        로그아웃
                    </a>
                <?php else: ?>
                    <a href="/user/login">로그인</a>
                    <a href="/user/register">회원가입</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">
