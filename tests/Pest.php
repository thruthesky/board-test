<?php

/**
 * 테스트 설정
 */

pest()->extend(Tests\TestCase::class)->in('Feature');

/**
 * 테스트용 인메모리 DB 초기화 헬퍼
 */
function setupTestDb(): \Utils\Db
{
    return \Utils\Db::createInMemory();
}

/**
 * 테스트용 사용자 생성 헬퍼
 */
function createTestUser(string $name = '테스트유저', string $email = 'test@example.com', string $password = 'password1234'): array
{
    $service = new \lib\user\UserService();
    return $service->register($name, $email, $password, $password);
}
