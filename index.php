<?php

/**
 * 웹 페이지 라우터 - 뷰 렌더링만 담당
 * API 엔드포인트는 api.php에서 처리
 * PHP 빌트인 서버: php -S localhost:8000 index.php
 */

// PHP 빌트인 서버에서 정적 파일(CSS, JS, 이미지 등) 직접 제공
if (php_sapi_name() === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $filePath = __DIR__ . $requestPath;

    // api.php 요청은 직접 실행
    if ($requestPath === '/api.php') {
        return false;
    }

    // PHP 파일은 라우터를 통해 처리하고, 나머지 정적 파일만 직접 제공
    if ($requestPath !== '/' && is_file($filePath) && !preg_match('/\.php$/', $requestPath)) {
        return false;
    }
}

// PSR-4 수동 오토로더
spl_autoload_register(function (string $class) {
    // 네임스페이스 구분자를 디렉토리 구분자로 변환
    $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// 세션 시작
session_start();

// 요청 정보 파싱
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// File Based Routing - URL 경로에서 자동으로 views/ 내 PHP 파일을 찾아 로드
$viewPath = ($uri === '/') ? '/home' : $uri;

// 경로 보안: '..' 등 디렉토리 탐색 방지
$viewPath = str_replace('..', '', $viewPath);

$viewFile = __DIR__ . '/views' . $viewPath . '.php';

if (file_exists($viewFile)) {
    require $viewFile;
} else {
    http_response_code(404);
    require __DIR__ . '/views/404.php';
}
