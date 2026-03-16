본 프로젝트는 PHP + SQLite 로 회원 가입/수정 기능과 게시판을 만드는 프로젝트입니다.

## 프로젝트 구조


### PHP 아키텍쳐

- `index.php`: 라우터 역할을 하는 파일로, 요청에 따라 적절한 컨트롤러를 호출합니다.
- `Utils/Db.php`: 데이터베이스 연결과 관련된 유틸리티 클래스입니다.
- `lib/`: 애플리케이션의 핵심 로직이 담긴 디렉토리입니다.

- `PSR-4` 오토로딩 규칙을 따르며, 각 도메인별로 컨트롤러, 엔티티, 리포지토리, 서비스 클래스를 별도의 폴더로 구분합니다.
- `views/`: 사용자 인터페이스를 담당하는 템플릿 파일들이 위치하는 디렉토리입니다. 각 도메인별로 폴더를 나누어 관리합니다.



- `Entity`: 데이터베이스 테이블과 매핑되는 클래스입니다. 예를 들어, `UserEntity`는 `users` 테이블의 구조를 반영합니다.
- `Repository`: 데이터베이스와의 상호작용을 담당하는 클래스입니다. 예를 들어, `UserRepository`는 `users` 테이블에 대한 CRUD 작업을 수행합니다.
- `Service`: 비즈니스 로직을 처리하는 클래스입니다. 예를 들어, `UserService`는 회원 가입과 관련된 로직을 처리합니다.
- `Controller`: HTTP 요청을 처리하고, 적절한 서비스를 호출하여 결과를 반환하는 클래스입니다. 예를 들어, `UserController`는 회원 관련 요청을 처리합니다.



### 폴더 구조

```
/
├── index.php
|-- data/database.sqlite
├── Utils/Db.php
├── lib/user/UserController.php
├── lib/user/UserEntity.php
├── lib/user/UserRepository.php
├── lib/user/UserService.php
├── lib/post/PostController.php
├── lib/post/PostEntity.php
├── lib/post/PostRepository.php
├── lib/post/PostService.php
├── views/user/register.php
├── views/user/edit.php
├── views/post/list.php
├── views/post/create.php
└── views/post/edit.php
```


### 데이터베이스
- SQLite 데이터베이스 파일은 `data/database.sqlite`에 위치합니다.
- 데이터베이스에는 `users` 테이블과 `posts` 테이블이 존재합니다.
- `users` 테이블은 회원 정보를 저장하며, `posts` 테이블은 게시글 정보를 저장합니다.

## 웹 사이트 접속

- 웹 사이트는 `http://localhost:8000`에서 접속할 수 있습니다.
- `index.php` 파일이 웹 서버의 루트 디렉토리에 위치하며, 모든 접속을 받는 라우터 역할을 합니다. 예를 들어, 회원 가입 페이지는 `http://localhost:8000/user/register`에서 접근할 수 있으며 -> `index.php`에서 -> `view/user/register.php`로 연결됩니다.
- `view/xxx/yyy.php` 파일은 `index.php`에서 라우팅된 요청에 따라 적절한 컨트롤러를 호출하여 정보를 가져오고, 해당 정보를 기반으로 렌더링됩니다.

## API 엔드포인트

- `index.php` 파일에서 라우팅된 요청에 따라 적절한 컨트롤러가 호출되어 API 엔드포인트가 처리됩니다. 예를 들어, `https://localhost:8000/index.php?method=user.register` 요청이 들어오면 `UserController`의 `register` 메서드가 호출됩니다.

### 회원 관련 엔드포인트
- `GET /user/register`: 회원 가입 페이지를 반환합니다.
- `POST /user/register`: 회원 가입 요청을 처리합니다.

## 테스트

### 테스트 프레임워크
- **PEST v3** (PHPUnit 기반)을 사용합니다.
- 테스트 파일은 `tests/` 디렉토리에 위치합니다.
  - `tests/Unit/`: 단위 테스트 (개별 클래스/메서드 단위)
  - `tests/Feature/`: 기능 테스트 (여러 컴포넌트 통합)
- 테스트 실행 명령어: `vendor/bin/pest`

### 테스트 워크플로우 (필수)
- **모든 코드 변경 작업 후 반드시 테스트를 실행해야 합니다.**
- 워크플로우:
  1. 코드 변경/추가 작업 수행
  2. 변경된 기능에 대한 테스트 코드 작성 또는 수정
  3. `vendor/bin/pest` 명령어로 전체 테스트 실행
  4. 모든 테스트가 통과하는지 확인
  5. 테스트 실패 시, 코드를 수정하고 다시 테스트 실행
- **새로운 기능 추가 시**: 해당 기능에 대한 테스트를 반드시 함께 작성합니다.
- **버그 수정 시**: 해당 버그를 재현하는 테스트를 먼저 작성한 후 수정합니다.
- **리팩토링 시**: 기존 테스트가 모두 통과하는지 확인합니다.