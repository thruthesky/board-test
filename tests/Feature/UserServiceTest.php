<?php

use lib\user\UserService;
use Utils\Db;

beforeEach(function () {
    setupTestDb();
    // 테스트용 세션 초기화
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    $_SESSION = [];
});

afterEach(function () {
    Db::resetInstance();
});

// === 회원 가입 ===

test('회원 가입이 성공한다', function () {
    $service = new UserService();
    $result = $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('회원 가입이 완료되었습니다.');
    expect($result['user_id'])->toBeGreaterThan(0);
});

test('빈 필드로 가입 시 실패한다', function () {
    $service = new UserService();
    $result = $service->register('', 'hong@example.com', 'pass1234', 'pass1234');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('모든 필드를 입력해주세요.');
});

test('잘못된 이메일 형식으로 가입 시 실패한다', function () {
    $service = new UserService();
    $result = $service->register('홍길동', 'invalid-email', 'pass1234', 'pass1234');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('유효한 이메일 주소를 입력해주세요.');
});

test('4자 미만 비밀번호로 가입 시 실패한다', function () {
    $service = new UserService();
    $result = $service->register('홍길동', 'hong@example.com', '123', '123');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('비밀번호는 4자 이상이어야 합니다.');
});

test('비밀번호 확인 불일치 시 가입 실패한다', function () {
    $service = new UserService();
    $result = $service->register('홍길동', 'hong@example.com', 'pass1234', 'different');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('비밀번호가 일치하지 않습니다.');
});

test('중복 이메일로 가입 시 실패한다', function () {
    $service = new UserService();
    $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');
    $result = $service->register('김철수', 'hong@example.com', 'pass5678', 'pass5678');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('이미 사용 중인 이메일입니다.');
});

// === 로그인 ===

test('올바른 정보로 로그인 성공한다', function () {
    $service = new UserService();
    $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');
    $result = $service->login('hong@example.com', 'pass1234');

    expect($result['success'])->toBeTrue();
    expect($result['user']['name'])->toBe('홍길동');
    expect($_SESSION['user_id'])->toBeGreaterThan(0);
});

test('잘못된 비밀번호로 로그인 실패한다', function () {
    $service = new UserService();
    $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');
    $result = $service->login('hong@example.com', 'wrongpass');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('이메일 또는 비밀번호가 올바르지 않습니다.');
});

test('존재하지 않는 이메일로 로그인 실패한다', function () {
    $service = new UserService();
    $result = $service->login('none@example.com', 'pass1234');

    expect($result['success'])->toBeFalse();
});

test('빈 필드로 로그인 시 실패한다', function () {
    $service = new UserService();
    $result = $service->login('', '');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('이메일과 비밀번호를 입력해주세요.');
});

// === 회원 정보 수정 ===

test('회원 정보를 수정할 수 있다', function () {
    $service = new UserService();
    $reg = $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');
    $userId = $reg['user_id'];

    $result = $service->update($userId, '김철수', 'kim@example.com');

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('회원 정보가 수정되었습니다.');
});

test('빈 이름으로 수정 시 실패한다', function () {
    $service = new UserService();
    $reg = $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');

    $result = $service->update($reg['user_id'], '', 'hong@example.com');

    expect($result['success'])->toBeFalse();
});

test('다른 사용자의 이메일로 수정 시 실패한다', function () {
    $service = new UserService();
    $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');
    $reg2 = $service->register('김철수', 'kim@example.com', 'pass1234', 'pass1234');

    $result = $service->update($reg2['user_id'], '김철수', 'hong@example.com');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('이미 사용 중인 이메일입니다.');
});

// === 비밀번호 변경 ===

test('비밀번호를 변경할 수 있다', function () {
    $service = new UserService();
    $reg = $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');

    $result = $service->changePassword($reg['user_id'], 'pass1234', 'newpass99', 'newpass99');

    expect($result['success'])->toBeTrue();

    // 새 비밀번호로 로그인 확인
    $loginResult = $service->login('hong@example.com', 'newpass99');
    expect($loginResult['success'])->toBeTrue();
});

test('현재 비밀번호가 틀리면 변경 실패한다', function () {
    $service = new UserService();
    $reg = $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');

    $result = $service->changePassword($reg['user_id'], 'wrongpass', 'newpass99', 'newpass99');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('현재 비밀번호가 올바르지 않습니다.');
});

test('새 비밀번호가 4자 미만이면 변경 실패한다', function () {
    $service = new UserService();
    $reg = $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');

    $result = $service->changePassword($reg['user_id'], 'pass1234', '12', '12');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('새 비밀번호는 4자 이상이어야 합니다.');
});

// === 로그인 상태 확인 ===

test('로그인 여부를 확인할 수 있다', function () {
    $service = new UserService();

    expect($service->isLoggedIn())->toBeFalse();

    $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');
    $service->login('hong@example.com', 'pass1234');

    expect($service->isLoggedIn())->toBeTrue();
});

test('현재 로그인한 사용자 정보를 조회할 수 있다', function () {
    $service = new UserService();
    $service->register('홍길동', 'hong@example.com', 'pass1234', 'pass1234');
    $service->login('hong@example.com', 'pass1234');

    $user = $service->getCurrentUser();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('홍길동');
    expect($user->email)->toBe('hong@example.com');
});
