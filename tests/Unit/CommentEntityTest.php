<?php

use lib\comment\CommentEntity;

test('빈 배열로 CommentEntity를 생성하면 기본값이 설정된다', function () {
    $entity = new CommentEntity();

    expect($entity->id)->toBeNull();
    expect($entity->post_id)->toBe(0);
    expect($entity->user_id)->toBe(0);
    expect($entity->parent_id)->toBeNull();
    expect($entity->content)->toBe('');
    expect($entity->created_at)->toBeNull();
    expect($entity->updated_at)->toBeNull();
    expect($entity->author_name)->toBeNull();
    expect($entity->children)->toBe([]);
});

test('데이터 배열로 CommentEntity를 생성하면 값이 할당된다', function () {
    $data = [
        'id' => 1,
        'post_id' => 10,
        'user_id' => 5,
        'parent_id' => 3,
        'content' => '테스트 댓글',
        'created_at' => '2026-01-01 00:00:00',
        'updated_at' => '2026-01-01 12:00:00',
        'author_name' => '테스트유저',
    ];

    $entity = new CommentEntity($data);

    expect($entity->id)->toBe(1);
    expect($entity->post_id)->toBe(10);
    expect($entity->user_id)->toBe(5);
    expect($entity->parent_id)->toBe(3);
    expect($entity->content)->toBe('테스트 댓글');
    expect($entity->created_at)->toBe('2026-01-01 00:00:00');
    expect($entity->updated_at)->toBe('2026-01-01 12:00:00');
    expect($entity->author_name)->toBe('테스트유저');
});

test('toArray()가 올바른 배열을 반환한다', function () {
    $entity = new CommentEntity([
        'id' => 1,
        'post_id' => 10,
        'user_id' => 5,
        'parent_id' => null,
        'content' => '댓글 내용',
        'author_name' => '유저',
        'created_at' => '2026-01-01 00:00:00',
        'updated_at' => '2026-01-01 00:00:00',
    ]);

    $array = $entity->toArray();

    expect($array)->toHaveKeys(['id', 'post_id', 'user_id', 'parent_id', 'content', 'author_name', 'created_at', 'updated_at', 'children']);
    expect($array['id'])->toBe(1);
    expect($array['content'])->toBe('댓글 내용');
    expect($array['children'])->toBe([]);
});

test('toArray()에 대댓글이 포함된다', function () {
    $parent = new CommentEntity([
        'id' => 1,
        'post_id' => 10,
        'user_id' => 5,
        'content' => '부모 댓글',
    ]);

    $child = new CommentEntity([
        'id' => 2,
        'post_id' => 10,
        'user_id' => 6,
        'parent_id' => 1,
        'content' => '대댓글',
    ]);

    $parent->children = [$child];
    $array = $parent->toArray();

    expect($array['children'])->toHaveCount(1);
    expect($array['children'][0]['content'])->toBe('대댓글');
    expect($array['children'][0]['parent_id'])->toBe(1);
});
