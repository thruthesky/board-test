<?php

use lib\user\UserEntity;

test('빈 배열로 생성 시 기본값이 설정된다', function () {
    $user = new UserEntity();

    expect($user->id)->toBeNull();
    expect($user->name)->toBe('');
    expect($user->email)->toBe('');
    expect($user->password)->toBe('');
    expect($user->created_at)->toBeNull();
    expect($user->updated_at)->toBeNull();
});

test('데이터 배열로 생성 시 값이 올바르게 할당된다', function () {
    $user = new UserEntity([
        'id' => 1,
        'name' => '홍길동',
        'email' => 'hong@example.com',
        'password' => 'hashed_password',
        'created_at' => '2024-01-01 00:00:00',
        'updated_at' => '2024-01-02 00:00:00',
    ]);

    expect($user->id)->toBe(1);
    expect($user->name)->toBe('홍길동');
    expect($user->email)->toBe('hong@example.com');
    expect($user->password)->toBe('hashed_password');
    expect($user->created_at)->toBe('2024-01-01 00:00:00');
    expect($user->updated_at)->toBe('2024-01-02 00:00:00');
});

test('toArray()에 비밀번호가 포함되지 않는다', function () {
    $user = new UserEntity([
        'id' => 1,
        'name' => '홍길동',
        'email' => 'hong@example.com',
        'password' => 'secret',
    ]);

    $array = $user->toArray();

    expect($array)->toHaveKeys(['id', 'name', 'email', 'created_at', 'updated_at']);
    expect($array)->not->toHaveKey('password');
});
