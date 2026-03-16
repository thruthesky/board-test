<?php

use lib\post\PostService;
use lib\user\UserService;
use Utils\Db;

beforeEach(function () {
    setupTestDb();
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    $_SESSION = [];

    // 테스트용 사용자 생성
    $userService = new UserService();
    $this->user1 = $userService->register('유저1', 'user1@test.com', 'pass1234', 'pass1234');
    $this->user2 = $userService->register('유저2', 'user2@test.com', 'pass1234', 'pass1234');
});

afterEach(function () {
    Db::resetInstance();
});

// === 게시글 작성 ===

test('게시글을 작성할 수 있다', function () {
    $service = new PostService();
    $result = $service->create($this->user1['user_id'], '테스트 제목', '테스트 내용');

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('게시글이 작성되었습니다.');
    expect($result['post_id'])->toBeGreaterThan(0);
});

test('빈 제목으로 작성 시 실패한다', function () {
    $service = new PostService();
    $result = $service->create($this->user1['user_id'], '', '내용');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('제목과 내용을 입력해주세요.');
});

test('빈 내용으로 작성 시 실패한다', function () {
    $service = new PostService();
    $result = $service->create($this->user1['user_id'], '제목', '');

    expect($result['success'])->toBeFalse();
});

test('200자 초과 제목으로 작성 시 실패한다', function () {
    $service = new PostService();
    $longTitle = str_repeat('가', 201);
    $result = $service->create($this->user1['user_id'], $longTitle, '내용');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('제목은 200자 이내로 입력해주세요.');
});

// === 게시글 목록 조회 ===

test('게시글 목록을 조회할 수 있다', function () {
    $service = new PostService();
    $service->create($this->user1['user_id'], '제목1', '내용1');
    $service->create($this->user1['user_id'], '제목2', '내용2');
    $service->create($this->user1['user_id'], '제목3', '내용3');

    $result = $service->getList(1, 10);

    expect($result['total_count'])->toBe(3);
    expect($result['posts'])->toHaveCount(3);
    expect($result['current_page'])->toBe(1);
    expect($result['total_pages'])->toBe(1);
});

test('페이징이 올바르게 동작한다', function () {
    $service = new PostService();
    for ($i = 1; $i <= 5; $i++) {
        $service->create($this->user1['user_id'], "제목{$i}", "내용{$i}");
    }

    $page1 = $service->getList(1, 2);
    $page2 = $service->getList(2, 2);

    expect($page1['posts'])->toHaveCount(2);
    expect($page1['total_count'])->toBe(5);
    expect($page1['total_pages'])->toBe(3);
    expect($page2['posts'])->toHaveCount(2);
});

// === 게시글 상세 조회 ===

test('게시글을 상세 조회할 수 있다', function () {
    $service = new PostService();
    $created = $service->create($this->user1['user_id'], '테스트 제목', '테스트 내용');
    $post = $service->getPost($created['post_id']);

    expect($post)->not->toBeNull();
    expect($post->title)->toBe('테스트 제목');
    expect($post->content)->toBe('테스트 내용');
    expect($post->author_name)->toBe('유저1');
});

test('존재하지 않는 게시글 조회 시 null을 반환한다', function () {
    $service = new PostService();
    $post = $service->getPost(9999);

    expect($post)->toBeNull();
});

// === 게시글 수정 ===

test('본인 게시글을 수정할 수 있다', function () {
    $service = new PostService();
    $created = $service->create($this->user1['user_id'], '원래 제목', '원래 내용');
    $result = $service->update($created['post_id'], $this->user1['user_id'], '수정 제목', '수정 내용');

    expect($result['success'])->toBeTrue();

    $post = $service->getPost($created['post_id']);
    expect($post->title)->toBe('수정 제목');
    expect($post->content)->toBe('수정 내용');
});

test('타인의 게시글 수정 시 실패한다', function () {
    $service = new PostService();
    $created = $service->create($this->user1['user_id'], '제목', '내용');
    $result = $service->update($created['post_id'], $this->user2['user_id'], '수정', '수정');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('수정 권한이 없습니다.');
});

test('존재하지 않는 게시글 수정 시 실패한다', function () {
    $service = new PostService();
    $result = $service->update(9999, $this->user1['user_id'], '제목', '내용');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('게시글을 찾을 수 없습니다.');
});

// === 게시글 삭제 ===

test('본인 게시글을 삭제할 수 있다', function () {
    $service = new PostService();
    $created = $service->create($this->user1['user_id'], '삭제할 글', '내용');
    $result = $service->delete($created['post_id'], $this->user1['user_id']);

    expect($result['success'])->toBeTrue();

    $post = $service->getPost($created['post_id']);
    expect($post)->toBeNull();
});

test('타인의 게시글 삭제 시 실패한다', function () {
    $service = new PostService();
    $created = $service->create($this->user1['user_id'], '제목', '내용');
    $result = $service->delete($created['post_id'], $this->user2['user_id']);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('삭제 권한이 없습니다.');
});

test('존재하지 않는 게시글 삭제 시 실패한다', function () {
    $service = new PostService();
    $result = $service->delete(9999, $this->user1['user_id']);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('게시글을 찾을 수 없습니다.');
});

// === 카테고리 ===

test('카테고리를 지정하여 게시글을 작성할 수 있다', function () {
    $service = new PostService();

    $r1 = $service->create($this->user1['user_id'], '토론 글', '내용', 'discussion');
    $r2 = $service->create($this->user1['user_id'], 'QnA 글', '내용', 'qna');
    $r3 = $service->create($this->user1['user_id'], '뉴스 글', '내용', 'news');

    expect($r1['success'])->toBeTrue();
    expect($r2['success'])->toBeTrue();
    expect($r3['success'])->toBeTrue();

    $post1 = $service->getPost($r1['post_id']);
    $post2 = $service->getPost($r2['post_id']);
    $post3 = $service->getPost($r3['post_id']);

    expect($post1->category)->toBe('discussion');
    expect($post2->category)->toBe('qna');
    expect($post3->category)->toBe('news');
});

test('유효하지 않은 카테고리로 작성 시 실패한다', function () {
    $service = new PostService();
    $result = $service->create($this->user1['user_id'], '제목', '내용', 'invalid');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('유효하지 않은 카테고리입니다.');
});

test('카테고리별 게시글 목록을 조회할 수 있다', function () {
    $service = new PostService();
    $service->create($this->user1['user_id'], '토론1', '내용', 'discussion');
    $service->create($this->user1['user_id'], '토론2', '내용', 'discussion');
    $service->create($this->user1['user_id'], 'QnA1', '내용', 'qna');
    $service->create($this->user1['user_id'], '뉴스1', '내용', 'news');

    $discussion = $service->getListByCategory('discussion');
    $qna = $service->getListByCategory('qna');
    $news = $service->getListByCategory('news');

    expect($discussion['total_count'])->toBe(2);
    expect($discussion['category'])->toBe('discussion');
    expect($qna['total_count'])->toBe(1);
    expect($news['total_count'])->toBe(1);
});

test('카테고리별 페이징이 올바르게 동작한다', function () {
    $service = new PostService();
    for ($i = 1; $i <= 15; $i++) {
        $service->create($this->user1['user_id'], "토론{$i}", '내용', 'discussion');
    }
    $service->create($this->user1['user_id'], 'QnA글', '내용', 'qna');

    $page1 = $service->getListByCategory('discussion', 1, 10);
    $page2 = $service->getListByCategory('discussion', 2, 10);

    expect($page1['total_count'])->toBe(15);
    expect($page1['posts'])->toHaveCount(10);
    expect($page1['total_pages'])->toBe(2);
    expect($page2['posts'])->toHaveCount(5);

    // qna는 1개만 있어야 함
    $qna = $service->getListByCategory('qna');
    expect($qna['total_count'])->toBe(1);
});

test('기본 카테고리는 discussion이다', function () {
    $service = new PostService();
    $result = $service->create($this->user1['user_id'], '기본 카테고리 글', '내용');

    $post = $service->getPost($result['post_id']);
    expect($post->category)->toBe('discussion');
});
