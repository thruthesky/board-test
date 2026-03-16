<?php

/**
 * PHPUnit 부트스트랩 파일
 * Composer 오토로더 + PSR-4 수동 오토로더 + 인메모리 DB 초기화
 */

// Composer 오토로더
require_once __DIR__ . '/../vendor/autoload.php';

// PSR-4 수동 오토로더 (lib/, Utils/ 네임스페이스)
spl_autoload_register(function (string $class) {
    $path = dirname(__DIR__) . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// 세션 시뮬레이션 (CLI에서는 session_start 불필요)
if (!isset($_SESSION)) {
    $_SESSION = [];
}

// 인메모리 DB로 초기화 (테스트마다 깨끗한 상태)
\Utils\Db::createInMemory();
