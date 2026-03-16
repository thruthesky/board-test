<?php
/**
 * 시드 데이터 생성 스크립트
 * 실행: php seed.php
 */

require __DIR__ . '/vendor/autoload.php';

$db = \Utils\Db::getInstance();
$pdo = $db->getPdo();

// 기존 데이터 초기화
$pdo->exec('DELETE FROM posts');
$pdo->exec('DELETE FROM users');

// 시드 사용자 생성
$stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
$users = [
    ['name' => '관리자', 'email' => 'admin@example.com', 'password' => password_hash('pass1234', PASSWORD_DEFAULT)],
    ['name' => '홍길동', 'email' => 'hong@example.com', 'password' => password_hash('pass1234', PASSWORD_DEFAULT)],
    ['name' => '김철수', 'email' => 'kim@example.com', 'password' => password_hash('pass1234', PASSWORD_DEFAULT)],
];

$userIds = [];
foreach ($users as $user) {
    $stmt->execute($user);
    $userIds[] = $pdo->lastInsertId();
}

echo "사용자 " . count($userIds) . "명 생성 완료\n";

// 카테고리별 125개 게시글 생성
$categories = [
    'discussion' => '토론',
    'qna' => '질문답변',
    'news' => '뉴스',
];

$postStmt = $pdo->prepare('INSERT INTO posts (user_id, category, title, content, created_at) VALUES (:user_id, :category, :title, :content, :created_at)');

$totalPosts = 0;
foreach ($categories as $category => $label) {
    for ($i = 1; $i <= 125; $i++) {
        $userId = $userIds[array_rand($userIds)];
        // 시간을 분산시켜 생성
        $daysAgo = 125 - $i;
        $createdAt = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

        $postStmt->execute([
            'user_id' => $userId,
            'category' => $category,
            'title' => "[{$label}] 게시글 #{$i} - 테스트 제목입니다",
            'content' => "{$label} 카테고리의 {$i}번째 게시글입니다.\n\n이것은 테스트 데이터로 자동 생성된 내용입니다.",
            'created_at' => $createdAt,
        ]);
        $totalPosts++;
    }
    echo "{$label}({$category}) 카테고리: 125개 게시글 생성 완료\n";
}

echo "\n총 {$totalPosts}개 게시글 생성 완료!\n";
echo "로그인 계정: admin@example.com / pass1234\n";
