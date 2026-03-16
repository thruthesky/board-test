<?php

use lib\post\PostEntity;

test('빈 배열로 생성 시 기본값이 설정된다', function () {
    $post = new PostEntity();

    expect($post->id)->toBeNull();
    expect($post->user_id)->toBe(0);
    expect($post->category)->toBe('discussion');
    expect($post->title)->toBe('');
    expect($post->content)->toBe('');
    expect($post->created_at)->toBeNull();
    expect($post->updated_at)->toBeNull();
    expect($post->author_name)->toBeNull();
});

test('데이터 배열로 생성 시 값이 올바르게 할당된다', function () {
    $post = new PostEntity([
        'id' => 1,
        'user_id' => 5,
        'category' => 'qna',
        'title' => '테스트 제목',
        'content' => '테스트 내용',
        'author_name' => '홍길동',
        'created_at' => '2024-01-01 00:00:00',
        'updated_at' => '2024-01-02 00:00:00',
    ]);

    expect($post->id)->toBe(1);
    expect($post->user_id)->toBe(5);
    expect($post->category)->toBe('qna');
    expect($post->title)->toBe('테스트 제목');
    expect($post->content)->toBe('테스트 내용');
    expect($post->author_name)->toBe('홍길동');
});

test('toArray()가 올바른 키와 값을 반환한다', function () {
    $post = new PostEntity([
        'id' => 1,
        'user_id' => 5,
        'title' => '제목',
        'content' => '내용',
        'author_name' => '작성자',
    ]);

    $array = $post->toArray();

    expect($array)->toHaveKeys(['id', 'user_id', 'category', 'title', 'content', 'author_name', 'created_at', 'updated_at']);
    expect($array['id'])->toBe(1);
    expect($array['user_id'])->toBe(5);
});
