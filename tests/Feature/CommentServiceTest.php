<?php

use lib\comment\CommentService;
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

    // 테스트용 게시글 생성
    $postService = new PostService();
    $result = $postService->create($this->user1['user_id'], '테스트 게시글', '게시글 내용');
    $this->postId = $result['post_id'];
});

afterEach(function () {
    Db::resetInstance();
});

// === 댓글 작성 ===

test('댓글을 작성할 수 있다', function () {
    $service = new CommentService();
    $result = $service->create($this->postId, $this->user1['user_id'], '테스트 댓글');

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('댓글이 작성되었습니다.');
    expect($result['comment_id'])->toBeGreaterThan(0);
});

test('빈 내용으로 댓글 작성 시 실패한다', function () {
    $service = new CommentService();
    $result = $service->create($this->postId, $this->user1['user_id'], '');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('댓글 내용을 입력해주세요.');
});

test('공백만 있는 내용으로 댓글 작성 시 실패한다', function () {
    $service = new CommentService();
    $result = $service->create($this->postId, $this->user1['user_id'], '   ');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('댓글 내용을 입력해주세요.');
});

test('1000자 초과 내용으로 댓글 작성 시 실패한다', function () {
    $service = new CommentService();
    $longContent = str_repeat('가', 1001);
    $result = $service->create($this->postId, $this->user1['user_id'], $longContent);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('댓글은 1000자 이내로 입력해주세요.');
});

test('존재하지 않는 게시글에 댓글 작성 시 실패한다', function () {
    $service = new CommentService();
    $result = $service->create(9999, $this->user1['user_id'], '댓글');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('게시글을 찾을 수 없습니다.');
});

// === 대댓글 작성 ===

test('대댓글을 작성할 수 있다', function () {
    $service = new CommentService();
    $parent = $service->create($this->postId, $this->user1['user_id'], '부모 댓글');
    $result = $service->create($this->postId, $this->user2['user_id'], '대댓글', $parent['comment_id']);

    expect($result['success'])->toBeTrue();
    expect($result['comment_id'])->toBeGreaterThan($parent['comment_id']);
});

test('존재하지 않는 부모 댓글에 대댓글 작성 시 실패한다', function () {
    $service = new CommentService();
    $result = $service->create($this->postId, $this->user1['user_id'], '대댓글', 9999);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('부모 댓글을 찾을 수 없습니다.');
});

test('대댓글의 대댓글을 무제한으로 작성할 수 있다', function () {
    $service = new CommentService();
    $depth1 = $service->create($this->postId, $this->user1['user_id'], '1단계 댓글');
    expect($depth1['success'])->toBeTrue();

    $depth2 = $service->create($this->postId, $this->user2['user_id'], '2단계 댓글', $depth1['comment_id']);
    expect($depth2['success'])->toBeTrue();

    $depth3 = $service->create($this->postId, $this->user1['user_id'], '3단계 댓글', $depth2['comment_id']);
    expect($depth3['success'])->toBeTrue();

    $depth4 = $service->create($this->postId, $this->user2['user_id'], '4단계 댓글', $depth3['comment_id']);
    expect($depth4['success'])->toBeTrue();

    // 트리 구조 확인
    $comments = $service->getCommentsByPostId($this->postId);
    expect($comments)->toHaveCount(1);
    expect($comments[0]->children)->toHaveCount(1);
    expect($comments[0]->children[0]->children)->toHaveCount(1);
    expect($comments[0]->children[0]->children[0]->children)->toHaveCount(1);
});

test('다른 게시글의 댓글을 부모로 지정할 수 없다', function () {
    $postService = new PostService();
    $post2 = $postService->create($this->user1['user_id'], '다른 게시글', '내용');

    $service = new CommentService();
    $parent = $service->create($post2['post_id'], $this->user1['user_id'], '다른 게시글의 댓글');

    // 다른 게시글의 댓글을 부모로 지정
    $result = $service->create($this->postId, $this->user1['user_id'], '대댓글', $parent['comment_id']);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('잘못된 부모 댓글입니다.');
});

// === 댓글 목록 조회 ===

test('게시글의 댓글 목록을 트리 구조로 조회할 수 있다', function () {
    $service = new CommentService();
    $parent1 = $service->create($this->postId, $this->user1['user_id'], '첫번째 댓글');
    $parent2 = $service->create($this->postId, $this->user2['user_id'], '두번째 댓글');
    $service->create($this->postId, $this->user2['user_id'], '첫번째 대댓글', $parent1['comment_id']);
    $service->create($this->postId, $this->user1['user_id'], '두번째 대댓글', $parent1['comment_id']);

    $comments = $service->getCommentsByPostId($this->postId);

    expect($comments)->toHaveCount(2); // 부모 댓글 2개
    expect($comments[0]->content)->toBe('첫번째 댓글');
    expect($comments[0]->children)->toHaveCount(2); // 대댓글 2개
    expect($comments[0]->children[0]->content)->toBe('첫번째 대댓글');
    expect($comments[0]->children[1]->content)->toBe('두번째 대댓글');
    expect($comments[1]->content)->toBe('두번째 댓글');
    expect($comments[1]->children)->toHaveCount(0);
});

test('댓글이 없는 게시글은 빈 배열을 반환한다', function () {
    $service = new CommentService();
    $comments = $service->getCommentsByPostId($this->postId);

    expect($comments)->toHaveCount(0);
});

test('댓글 수를 조회할 수 있다', function () {
    $service = new CommentService();
    $parent = $service->create($this->postId, $this->user1['user_id'], '댓글1');
    $service->create($this->postId, $this->user2['user_id'], '댓글2');
    $service->create($this->postId, $this->user2['user_id'], '대댓글', $parent['comment_id']);

    $count = $service->getCommentCount($this->postId);

    expect($count)->toBe(3); // 댓글 2 + 대댓글 1
});

// === 댓글 수정 ===

test('본인의 댓글을 수정할 수 있다', function () {
    $service = new CommentService();
    $created = $service->create($this->postId, $this->user1['user_id'], '원래 댓글');
    $result = $service->update($created['comment_id'], $this->user1['user_id'], '수정된 댓글');

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('댓글이 수정되었습니다.');

    // 수정된 내용 확인
    $comments = $service->getCommentsByPostId($this->postId);
    expect($comments[0]->content)->toBe('수정된 댓글');
});

test('타인의 댓글 수정 시 실패한다', function () {
    $service = new CommentService();
    $created = $service->create($this->postId, $this->user1['user_id'], '댓글');
    $result = $service->update($created['comment_id'], $this->user2['user_id'], '수정 시도');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('수정 권한이 없습니다.');
});

test('존재하지 않는 댓글 수정 시 실패한다', function () {
    $service = new CommentService();
    $result = $service->update(9999, $this->user1['user_id'], '수정');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('댓글을 찾을 수 없습니다.');
});

test('빈 내용으로 댓글 수정 시 실패한다', function () {
    $service = new CommentService();
    $created = $service->create($this->postId, $this->user1['user_id'], '원래 댓글');
    $result = $service->update($created['comment_id'], $this->user1['user_id'], '');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('댓글 내용을 입력해주세요.');
});

test('1000자 초과 내용으로 댓글 수정 시 실패한다', function () {
    $service = new CommentService();
    $created = $service->create($this->postId, $this->user1['user_id'], '원래 댓글');
    $longContent = str_repeat('가', 1001);
    $result = $service->update($created['comment_id'], $this->user1['user_id'], $longContent);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('댓글은 1000자 이내로 입력해주세요.');
});

// === 댓글 삭제 ===

test('본인의 댓글을 삭제할 수 있다', function () {
    $service = new CommentService();
    $created = $service->create($this->postId, $this->user1['user_id'], '삭제할 댓글');
    $result = $service->delete($created['comment_id'], $this->user1['user_id']);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('댓글이 삭제되었습니다.');

    $comments = $service->getCommentsByPostId($this->postId);
    expect($comments)->toHaveCount(0);
});

test('타인의 댓글 삭제 시 실패한다', function () {
    $service = new CommentService();
    $created = $service->create($this->postId, $this->user1['user_id'], '댓글');
    $result = $service->delete($created['comment_id'], $this->user2['user_id']);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('삭제 권한이 없습니다.');
});

test('존재하지 않는 댓글 삭제 시 실패한다', function () {
    $service = new CommentService();
    $result = $service->delete(9999, $this->user1['user_id']);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('댓글을 찾을 수 없습니다.');
});

test('부모 댓글 삭제 시 대댓글도 함께 삭제된다', function () {
    $service = new CommentService();
    $parent = $service->create($this->postId, $this->user1['user_id'], '부모 댓글');
    $service->create($this->postId, $this->user2['user_id'], '대댓글1', $parent['comment_id']);
    $service->create($this->postId, $this->user2['user_id'], '대댓글2', $parent['comment_id']);

    expect($service->getCommentCount($this->postId))->toBe(3);

    $service->delete($parent['comment_id'], $this->user1['user_id']);

    expect($service->getCommentCount($this->postId))->toBe(0);
});
