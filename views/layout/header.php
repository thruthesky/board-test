<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '게시판') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-light: #eef2ff;
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --primary-500: #6366f1;
            --primary-600: #4f46e5;
            --primary-700: #4338ca;
            --accent: #8b5cf6;
            --accent-light: #f5f3ff;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --danger-light: #fef2f2;
            --success: #10b981;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;
            --success-text: #065f46;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --error-text: #991b1b;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --radius: 16px;
            --radius-sm: 10px;
            --radius-xs: 6px;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.03);
            --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.07), 0 8px 10px -6px rgba(0,0,0,0.03);
            --shadow-xl: 0 20px 40px -8px rgba(0,0,0,0.1);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Noto Sans KR', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(180deg, #f0f4ff 0%, var(--gray-50) 300px);
            color: var(--gray-800);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            min-height: 100vh;
        }

        .container { max-width: 880px; margin: 0 auto; padding: 0 24px; }

        /* ==================== 네비게이션 ==================== */
        nav {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            padding: 0;
            margin-bottom: 40px;
            box-shadow: 0 1px 0 rgba(255,255,255,0.05), 0 4px 20px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        nav .container {
            max-width: 1100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }
        nav a {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            margin-left: 4px;
            padding: 7px 14px;
            border-radius: var(--radius-xs);
            font-size: 0.88em;
            font-weight: 500;
            transition: var(--transition);
            letter-spacing: -0.01em;
        }
        nav a:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }
        nav .logo {
            font-size: 1.25em;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            margin-left: 0;
            padding: 0;
            letter-spacing: -0.03em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        nav .logo::before {
            content: '';
            display: inline-block;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 8px;
            flex-shrink: 0;
        }
        nav .logo:hover { background: none; color: #fff; }
        nav .nav-links { display: flex; align-items: center; gap: 2px; }
        nav .nav-write-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff !important;
            padding: 7px 16px;
            border-radius: var(--radius-xs);
            font-weight: 600;
            font-size: 0.85em;
        }
        nav .nav-write-btn:hover {
            opacity: 0.9;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            transform: translateY(-1px);
        }

        /* ==================== 카드 ==================== */
        .card {
            background: #fff;
            border-radius: var(--radius);
            padding: 32px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            border: 1px solid rgba(226,232,240,0.6);
            transition: var(--transition);
        }
        .card:hover { box-shadow: var(--shadow-md); }
        .card h1, .card h2 {
            color: var(--gray-900);
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .card h1 { font-size: 1.75em; }
        .card h2 {
            font-size: 1.3em;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 20px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* ==================== 폼 ==================== */
        .form-group { margin-bottom: 22px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.88em;
            color: var(--gray-700);
            letter-spacing: -0.01em;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 11px 16px;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 0.95em;
            font-family: inherit;
            color: var(--gray-800);
            background: #fff;
            transition: var(--transition);
            outline: none;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light), 0 1px 2px rgba(0,0,0,0.04);
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

        /* ==================== 버튼 ==================== */
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
            letter-spacing: -0.01em;
        }
        .btn:active { transform: scale(0.97); }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: #fff;
            box-shadow: 0 1px 3px rgba(99,102,241,0.3), 0 1px 2px rgba(99,102,241,0.1);
        }
        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(99,102,241,0.35), 0 2px 4px rgba(99,102,241,0.15);
            transform: translateY(-1px);
        }
        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-hover) 100%);
            color: #fff;
            box-shadow: 0 1px 3px rgba(239,68,68,0.25);
        }
        .btn-danger:hover {
            box-shadow: 0 4px 12px rgba(239,68,68,0.35);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #fff;
            color: var(--gray-600);
            border: 1.5px solid var(--gray-200);
            box-shadow: var(--shadow-xs);
        }
        .btn-secondary:hover {
            background: var(--gray-50);
            color: var(--gray-800);
            border-color: var(--gray-300);
            box-shadow: var(--shadow);
        }

        /* ==================== 알림 ==================== */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 24px;
            font-size: 0.9em;
            font-weight: 500;
            line-height: 1.6;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert::before { font-size: 1.1em; flex-shrink: 0; }
        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }
        .alert-success::before { content: '\2713'; }
        .alert-error {
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
        }
        .alert-error::before { content: '\26A0'; }

        /* ==================== 테이블 ==================== */
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        table th, table td {
            padding: 14px 16px;
            text-align: left;
        }
        table th {
            background: var(--gray-50);
            font-weight: 600;
            font-size: 0.8em;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 2px solid var(--gray-200);
        }
        table th:first-child { border-radius: var(--radius-xs) 0 0 0; }
        table th:last-child { border-radius: 0 var(--radius-xs) 0 0; }
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

        /* ==================== 페이지네이션 ==================== */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-100);
        }
        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--gray-600);
            font-size: 0.85em;
            font-weight: 500;
            transition: var(--transition);
            background: #fff;
        }
        .pagination a:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        .pagination .active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(99,102,241,0.3);
        }
        .pagination .dots {
            border: none;
            background: transparent;
            color: var(--gray-400);
            min-width: 28px;
            padding: 0;
            cursor: default;
        }

        /* ==================== 탭 ==================== */
        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 28px;
            border-bottom: 2px solid var(--gray-200);
            overflow-x: auto;
        }
        .tab {
            padding: 12px 24px;
            text-decoration: none;
            color: var(--gray-500);
            font-weight: 500;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: var(--transition);
            font-size: 0.95em;
            white-space: nowrap;
        }
        .tab:hover { color: var(--primary); }
        .tab.active { color: var(--primary); border-bottom-color: var(--primary); font-weight: 700; }

        /* ==================== 히어로 ==================== */
        .hero {
            border-radius: var(--radius);
            padding: 60px 52px;
            color: #fff;
            margin-bottom: 36px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
            min-height: 260px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(79,70,229,0.25);
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero h1 { font-size: 2.2em; font-weight: 800; margin-bottom: 12px; color: #fff; position: relative; z-index: 1; letter-spacing: -0.03em; }
        .hero p { font-size: 1.05em; opacity: 0.9; position: relative; z-index: 1; font-weight: 400; line-height: 1.6; }
        .hero .hero-actions { margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap; position: relative; z-index: 1; }
        .hero .btn-hero {
            display: inline-flex; align-items: center;
            padding: 12px 28px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.9em;
            text-decoration: none;
            transition: var(--transition);
            letter-spacing: -0.01em;
        }
        .hero .btn-hero-primary {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .hero .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .hero .btn-hero-outline {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.35);
            backdrop-filter: blur(8px);
        }
        .hero .btn-hero-outline:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }

        /* ==================== 홈 그리드 ==================== */
        .home-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        .home-grid .card { margin-bottom: 0; padding: 0; overflow: hidden; }
        .home-grid .card:hover { transform: translateY(-4px); box-shadow: var(--shadow-xl); }

        /* 카테고리 카드 커버 이미지 */
        .cat-cover {
            width: 100%;
            height: 130px;
            object-fit: cover;
            display: block;
            transition: var(--transition-slow);
        }
        .home-grid .card:hover .cat-cover { transform: scale(1.05); }
        .cat-cover-wrap { overflow: hidden; }
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
            letter-spacing: -0.02em;
        }
        .cat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.73em;
            font-weight: 700;
        }
        .cat-badge-indigo { background: var(--primary-light); color: var(--primary); }
        .cat-badge-emerald { background: #ecfdf5; color: #059669; }
        .cat-badge-amber { background: #fffbeb; color: #d97706; }
        .cat-header a {
            font-size: 0.83em;
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

        /* ==================== 아바타 ==================== */
        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
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
        .nav-user .avatar { border: 2px solid rgba(255,255,255,0.2); }

        /* ==================== 파일 업로드 ==================== */
        .file-upload-area {
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius);
            padding: 28px;
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
            box-shadow: var(--shadow-xs);
        }
        .file-preview-item img { width: 100%; height: 100%; object-fit: cover; }
        .file-preview-item .remove-btn {
            position: absolute; top: 4px; right: 4px;
            width: 22px; height: 22px;
            background: rgba(0,0,0,0.6); color: #fff;
            border: none; border-radius: 50%;
            cursor: pointer; font-size: 12px;
            display: flex; align-items: center; justify-content: center;
            transition: var(--transition);
        }
        .file-preview-item .remove-btn:hover { background: var(--danger); }
        .file-preview-item .file-name {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.5); color: #fff;
            font-size: 0.65em; padding: 3px 6px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* 작성자 정보 */
        .author-info {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ==================== 게시글 상세 ==================== */
        .post-meta {
            color: var(--gray-500);
            margin: -8px 0 24px;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-100);
        }
        .post-meta .author-name {
            font-weight: 600;
            color: var(--gray-800);
        }
        .post-meta .date {
            color: var(--gray-400);
            font-size: 0.88em;
        }
        .post-content {
            min-height: 200px;
            white-space: pre-wrap;
            line-height: 1.9;
            color: var(--gray-700);
            font-size: 0.98em;
            padding: 20px 0;
        }
        .post-actions {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-100);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* 댓글 */
        .comment-section { margin-top: 24px; }
        .comment-section h3 {
            margin-bottom: 20px;
            font-size: 1.1em;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .comment-section h3 .count-badge {
            background: var(--primary-light);
            color: var(--primary);
            font-size: 0.75em;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 700;
        }

        /* ==================== 유틸리티 ==================== */
        .text-right { text-align: right; }
        .mb-20 { margin-bottom: 20px; }
        .wide-container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

        /* 선택 스타일 */
        ::selection {
            background: var(--primary-light);
            color: var(--primary-700);
        }

        /* 스크롤바 */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--gray-400); }

        /* ==================== 반응형 ==================== */
        @media (max-width: 1024px) {
            .home-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
            .wide-container { max-width: 100%; padding: 0 20px; }
            .cat-cover { height: 100px; }
        }
        @media (max-width: 768px) {
            .home-grid { grid-template-columns: 1fr; max-width: 500px; margin-left: auto; margin-right: auto; }
            .cat-cover { height: 140px; }
            nav .nav-links a { font-size: 0.82em; padding: 6px 10px; }
        }
        @media (max-width: 640px) {
            .container { padding: 0 16px; }
            nav { margin-bottom: 28px; }
            nav .container { height: 56px; }
            nav a { padding: 6px 8px; font-size: 0.8em; margin-left: 2px; }
            nav .logo { font-size: 1.05em; }
            nav .logo::before { width: 24px; height: 24px; border-radius: 6px; }
            .card { padding: 24px 20px; border-radius: 12px; }
            .card h2 { font-size: 1.15em; }
            .home-grid .card { padding: 0; }
            table th, table td { padding: 10px 12px; }
            .hero { padding: 40px 24px; min-height: 200px; }
            .hero h1 { font-size: 1.5em; }
            .wide-container { padding: 0 16px; }
            .btn { padding: 9px 18px; font-size: 0.85em; }
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
                    <a href="/post/create" class="nav-write-btn">글쓰기</a>
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
                    <a href="/user/register" class="nav-write-btn">회원가입</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">
