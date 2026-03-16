<?php
// 로그아웃 처리
$service = new \lib\user\UserService();
$service->logout();
header('Location: /');
exit;
