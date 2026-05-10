# LifeOS Backend

API backend cho ứng dụng LifeOS.

**Stack:** Laravel 13 · PHP 8.3 · PostgreSQL · Laravel Sanctum · Scramble (OpenAPI)

---

## Mục lục

- [Kiến trúc](#kiến-trúc)
  - [Tổng quan](#tổng-quan)
  - [Cấu trúc thư mục](#cấu-trúc-thư-mục)
  - [Luồng xử lý request](#luồng-xử-lý-request)
  - [Ưu điểm](#ưu-điểm)
  - [Nhược điểm & đánh đổi](#nhược-điểm--đánh-đổi)
- [Cài đặt](#cài-đặt)
- [Phát triển](#phát-triển)
  - [Scaffold commands](#scaffold-commands)
  - [Thêm một domain mới](#thêm-một-domain-mới)
  - [Thêm một tính năng vào domain có sẵn](#thêm-một-tính-năng-vào-domain-có-sẵn)
  - [Thay thế implementation (ví dụ: đổi storage)](#thay-thế-implementation)
- [API Endpoints](#api-endpoints)
- [OpenAPI / API Documentation](#openapi--api-documentation)
  - [Cách Scramble hoạt động](#cách-scramble-hoạt-động)
  - [Annotate đúng cách](#annotate-đúng-cách)
  - [Xem docs UI (local)](#xem-docs-ui-local)
  - [Export spec ra file](#export-spec-ra-file)
  - [Truy cập spec qua API (production)](#truy-cập-spec-qua-api-production)
  - [Tích hợp với Postman / Insomnia](#tích-hợp-với-postman--insomnia)
  - [Workflow phát triển](#workflow-phát-triển)

---

## Kiến trúc

### Tổng quan

Project áp dụng **Domain-Driven Design (DDD)** kết hợp **Repository Pattern** và **Service Container** của Laravel. Mỗi domain là một module khép kín — toàn bộ business logic, infrastructure, models, requests, resources của domain đó nằm cùng một thư mục.

```
app/
├── Domains/                  ← Toàn bộ business domains
│   ├── Auth/
│   ├── Knowledge/
│   ├── Task/
│   └── Shared/               ← Dùng chung giữa các domains
├── Presentation/             ← HTTP entry point (Controllers)
│   └── Controllers/Api/V1/
├── Models/                   ← Eloquent User model (dùng bởi Sanctum)
└── Console/Commands/         ← Artisan commands (scaffold + utilities)
```

### Cấu trúc thư mục

Mỗi domain có cấu trúc nhất quán:

```
app/Domains/{Domain}/
├── Data/                     ← DTOs (Data Transfer Objects)
│   └── FolderData.php        ← Immutable readonly, carry validated input
│
├── Domain/                   ← Business logic (không biết HTTP, không biết DB)
│   ├── Contracts/            ← Repository interfaces
│   │   └── FolderRepositoryInterface.php
│   └── Folder/               ← Actions nhóm theo entity
│       ├── CreateFolderAction.php
│       ├── UpdateFolderAction.php
│       └── DeleteFolderAction.php
│
├── Infrastructure/           ← Implementations hạ tầng
│   └── Repositories/
│       └── EloquentFolderRepository.php   ← Eloquent implementation
│
├── Models/                   ← Eloquent models (scopes, relationships, business rules)
│   └── Folder.php
│
├── Providers/                ← Bind interface → implementation
│   └── KnowledgeServiceProvider.php
│
├── Requests/                 ← FormRequests (validation + DTO conversion)
│   ├── CreateFolderRequest.php
│   └── UpdateFolderRequest.php
│
├── Resources/                ← JSON API Resources (response transformation)
│   └── FolderResource.php
│
└── Exceptions/               ← Domain-specific exceptions
    └── FolderNotFoundException.php
```

**Controllers** nằm tách riêng ở `Presentation/` vì chúng là HTTP adapter — không thuộc về bất kỳ domain nào:

```
app/Presentation/Controllers/Api/V1/
├── Auth/
│   └── AuthController.php
├── Knowledge/
│   ├── FolderController.php
│   ├── NoteController.php
│   └── SnippetController.php
└── Task/
    └── TaskController.php
```

### Luồng xử lý request

```
HTTP Request
    │
    ▼
FormRequest          ← Validate input, convert to DTO
    │
    ▼
Controller           ← Dispatch only: gọi Action hoặc Repository, trả Resource
    │
    ├──► Repository  ← index/show: query trực tiếp qua interface
    │
    └──► Action      ← store/update/delete: business logic
              │
              ▼
         Repository  ← Action gọi repository interface (không biết Eloquent)
              │
              ▼
    EloquentRepository  ← Thực thi query, trả Model
              │
              ▼
         Model        ← Eloquent ORM, scopes, relationships
```

**Ví dụ cụ thể — tạo folder:**

```
POST /api/v1/folders
    │
    ▼
CreateFolderRequest::toFolderData()   → FolderData (DTO)
    │
    ▼
FolderController::store()             → gọi CreateFolderAction
    │
    ▼
CreateFolderAction::execute()         → gọi FolderRepositoryInterface::create()
    │
    ▼
EloquentFolderRepository::create()    → Folder::create(...)
    │
    ▼
FolderResource                        → JSON response
```

### Ưu điểm

**Tách biệt rõ ràng theo domain**
Toàn bộ code liên quan đến `Knowledge` nằm trong `app/Domains/Knowledge/`. Khi cần sửa hoặc xóa một domain, chỉ cần xử lý một thư mục.

**Business logic không phụ thuộc infrastructure**
Actions chỉ biết `FolderRepositoryInterface` — không biết Eloquent, không biết PostgreSQL. Có thể swap sang MongoDB hay in-memory repository mà không sửa một dòng business logic nào.

**Dễ mở rộng không phá vỡ code cũ**
Muốn thêm `CreateFolderActionV2`? Tạo class mới, bind lại trong ServiceProvider. Controller không cần sửa vì nó inject interface.

**Controller thuần HTTP**
Controller chỉ làm 3 việc: nhận request → gọi action/repository → trả resource. Không có query Eloquent, không có business logic, không có `if/else` phức tạp.

**Scaffold nhanh**
6 Artisan commands tạo đầy đủ boilerplate trong vài giây.

**Testability cao**
Mỗi Action có thể test độc lập bằng cách mock repository interface. Không cần database thật.

### Nhược điểm & đánh đổi

**Nhiều file hơn**
Một tính năng đơn giản cần: Data + Action + Repository interface + Repository implementation + Request + Resource. Với team nhỏ hoặc prototype, đây là overhead đáng kể.

**Learning curve**
Developer mới cần hiểu luồng: Request → DTO → Action → Repository → Model trước khi có thể contribute hiệu quả.

**Domains không hoàn toàn độc lập**
`Knowledge` models (`Folder`, `Note`, `Snippet`) vẫn có relationship với `User` từ `App\Models`. Đây là đánh đổi có chủ ý — User là shared entity, không thuộc riêng domain nào.

**Không có CQRS thuần**
Read (index/show) vẫn đi qua repository trong controller thay vì có Query handler riêng. Đủ cho scale hiện tại, có thể tách thêm khi cần.

---

## Cài đặt

### Yêu cầu

- PHP >= 8.3
- PostgreSQL >= 14
- Composer >= 2.x

### Các bước

**1. Clone và cài dependencies**

```bash
git clone <repo-url>
cd be_life_os
composer install
```

**2. Cấu hình môi trường**

```bash
cp .env.example .env
php artisan key:generate
```

Chỉnh sửa `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=be_life_os
DB_USERNAME=your_username
DB_PASSWORD=your_password

APP_URL=http://localhost:8000
```

**3. Chạy migrations**

```bash
php artisan migrate
```

**4. Tạo storage symlink** (cho avatar upload)

```bash
php artisan storage:link
```

**5. Khởi động server**

```bash
php artisan serve
```

API sẽ chạy tại `http://localhost:8000/api/v1`.

---

## Phát triển

### Scaffold commands

Tất cả commands đều kiểm tra file đã tồn tại trước khi tạo — an toàn khi chạy nhiều lần.

| Command | Tạo ra | Ví dụ |
|---|---|---|
| `make:domain-model` | Model + Repository interface + Eloquent implementation | `php artisan make:domain-model Knowledge/Tag` |
| `make:domain-data` | DTO (readonly class) | `php artisan make:domain-data Knowledge/TagData` |
| `make:domain-action` | Action class | `php artisan make:domain-action Knowledge/Tag/CreateTagAction` |
| `make:domain-repository` | Repository interface + Eloquent implementation | `php artisan make:domain-repository Knowledge/Tag` |
| `make:domain-request` | FormRequest | `php artisan make:domain-request Knowledge/CreateTagRequest` |
| `make:domain-resource` | JsonResource | `php artisan make:domain-resource Knowledge/TagResource` |

**Options:**

```bash
# Thêm SoftDeletes vào model
php artisan make:domain-model Knowledge/Tag --soft-deletes
```

---

### Thêm một domain mới

Ví dụ thêm domain `Finance` với entity `Transaction`.

**Bước 1 — Scaffold files**

```bash
php artisan make:domain-model    Finance/Transaction --soft-deletes
php artisan make:domain-data     Finance/TransactionData
php artisan make:domain-action   Finance/Transaction/CreateTransactionAction
php artisan make:domain-action   Finance/Transaction/UpdateTransactionAction
php artisan make:domain-action   Finance/Transaction/DeleteTransactionAction
php artisan make:domain-request  Finance/CreateTransactionRequest
php artisan make:domain-request  Finance/UpdateTransactionRequest
php artisan make:domain-resource Finance/TransactionResource
```

**Bước 2 — Tạo ServiceProvider**

```bash
# Tạo file thủ công
touch app/Domains/Finance/Providers/FinanceServiceProvider.php
```

```php
<?php

declare(strict_types=1);

namespace App\Domains\Finance\Providers;

use App\Domains\Finance\Domain\Contracts\TransactionRepositoryInterface;
use App\Domains\Finance\Infrastructure\Repositories\EloquentTransactionRepository;
use Illuminate\Support\ServiceProvider;

final class FinanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            EloquentTransactionRepository::class,
        );
    }
}
```

**Bước 3 — Đăng ký provider trong AppServiceProvider**

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->register(AuthServiceProvider::class);
    $this->app->register(KnowledgeServiceProvider::class);
    $this->app->register(TaskServiceProvider::class);
    $this->app->register(FinanceServiceProvider::class); // ← thêm dòng này
}
```

**Bước 4 — Tạo migration**

```bash
php artisan make:migration create_transactions_table
php artisan migrate
```

**Bước 5 — Tạo Controller**

```bash
# Tạo thủ công
touch app/Presentation/Controllers/Api/V1/Finance/TransactionController.php
```

```php
<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1\Finance;

use App\Domains\Finance\Domain\Contracts\TransactionRepositoryInterface;
use App\Domains\Finance\Domain\Transaction\CreateTransactionAction;
// ...

final class TransactionController
{
    public function __construct(
        private readonly TransactionRepositoryInterface $repository,
        private readonly CreateTransactionAction        $createAction,
        // ...
    ) {}

    // implement index, store, show, update, destroy
}
```

**Bước 6 — Đăng ký routes**

```php
// routes/api.php
use App\Presentation\Controllers\Api\V1\Finance\TransactionController;

Route::middleware('auth:sanctum')->group(function (): void {
    // ...
    Route::apiResource('transactions', TransactionController::class);
});
```

---

### Thêm một tính năng vào domain có sẵn

Ví dụ thêm tính năng `duplicate` một Note.

**Bước 1 — Tạo Action**

```bash
php artisan make:domain-action Knowledge/Note/DuplicateNoteAction
```

Implement trong `app/Domains/Knowledge/Domain/Note/DuplicateNoteAction.php`:

```php
final readonly class DuplicateNoteAction
{
    public function __construct(
        private NoteRepositoryInterface $repository,
    ) {}

    public function execute(int $noteId, int $userId): Note
    {
        $original = $this->repository->findById($noteId);

        if (!$original) {
            throw new NoteNotFoundException($noteId);
        }

        if (!$original->canBeViewedBy($userId)) {
            throw new UnauthorizedException('duplicate this note');
        }

        return $this->repository->create(NoteData::fromArray([
            'user_id'   => $userId,
            'folder_id' => $original->folder_id,
            'title'     => $original->title . ' (copy)',
            'content'   => $original->content,
            'type'      => $original->type,
            'is_pinned' => false,
        ]));
    }
}
```

**Bước 2 — Thêm method vào Controller**

```php
// app/Presentation/Controllers/Api/V1/Knowledge/NoteController.php

public function __construct(
    // ...
    private readonly DuplicateNoteAction $duplicateAction,  // ← inject thêm
) {}

/** POST /api/v1/notes/{note}/duplicate */
public function duplicate(Request $request, int $note): JsonResponse
{
    try {
        $model = $this->duplicateAction->execute($note, $request->user()->id);
        return response()->json(new NoteResource($model), Response::HTTP_CREATED);
    } catch (NoteNotFoundException $e) {
        return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
    } catch (UnauthorizedException $e) {
        return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
    }
}
```

**Bước 3 — Đăng ký route**

```php
// routes/api.php
Route::apiResource('notes', NoteController::class);
Route::post('notes/{note}/duplicate', [NoteController::class, 'duplicate']);
```

---

### Thay thế implementation

Ví dụ muốn lưu snippets lên S3 thay vì database.

Chỉ cần tạo implementation mới và bind lại — không sửa Action hay Controller:

```php
// app/Domains/Knowledge/Infrastructure/Repositories/S3SnippetRepository.php

final class S3SnippetRepository implements SnippetRepositoryInterface
{
    // implement các methods dùng S3
}
```

```php
// app/Domains/Knowledge/Providers/KnowledgeServiceProvider.php

$this->app->bind(
    SnippetRepositoryInterface::class,
    S3SnippetRepository::class,  // ← đổi dòng này
);
```

---

## API Endpoints

Tất cả endpoints đều có prefix `/api/v1`.

### Auth

| Method | Endpoint | Auth | Mô tả |
|---|---|---|---|
| `POST` | `/auth/register` | — | Đăng ký tài khoản |
| `POST` | `/auth/login` | — | Đăng nhập, nhận Bearer token |
| `GET` | `/auth/me` | ✓ | Thông tin user hiện tại |
| `PUT` | `/auth/me` | ✓ | Cập nhật profile |
| `POST` | `/auth/avatar` | ✓ | Upload avatar |
| `DELETE` | `/auth/avatar` | ✓ | Xóa avatar |
| `PUT` | `/auth/password` | ✓ | Đổi mật khẩu |
| `POST` | `/auth/logout` | ✓ | Đăng xuất thiết bị hiện tại |
| `POST` | `/auth/logout-all` | ✓ | Đăng xuất tất cả thiết bị |

### Knowledge — Folders

| Method | Endpoint | Mô tả |
|---|---|---|
| `GET` | `/folders` | Danh sách folders gốc (có children) |
| `POST` | `/folders` | Tạo folder |
| `GET` | `/folders/{id}` | Chi tiết folder |
| `PUT` | `/folders/{id}` | Cập nhật folder |
| `DELETE` | `/folders/{id}` | Xóa folder |

### Knowledge — Notes

| Method | Endpoint | Query params | Mô tả |
|---|---|---|---|
| `GET` | `/notes` | `folder_id`, `is_pinned`, `type`, `search` | Danh sách notes (paginated) |
| `POST` | `/notes` | | Tạo note |
| `GET` | `/notes/{id}` | | Chi tiết note |
| `PUT` | `/notes/{id}` | | Cập nhật note |
| `DELETE` | `/notes/{id}` | | Xóa note |

### Knowledge — Snippets

| Method | Endpoint | Query params | Mô tả |
|---|---|---|---|
| `GET` | `/snippets` | `folder_id`, `language`, `search` | Danh sách snippets (paginated) |
| `POST` | `/snippets` | | Tạo snippet |
| `GET` | `/snippets/{id}` | | Chi tiết snippet |
| `PUT` | `/snippets/{id}` | | Cập nhật snippet |
| `DELETE` | `/snippets/{id}` | | Xóa snippet |

### Tasks

| Method | Endpoint | Query params | Mô tả |
|---|---|---|---|
| `GET` | `/tasks` | `status`, `priority`, `overdue`, `due_today`, `due_soon` | Danh sách tasks (paginated) |
| `POST` | `/tasks` | | Tạo task |
| `GET` | `/tasks/{id}` | | Chi tiết task |
| `PUT` | `/tasks/{id}` | | Cập nhật task |
| `DELETE` | `/tasks/{id}` | | Xóa task |

**Authentication:** Tất cả endpoints (trừ register/login) yêu cầu header:

```
Authorization: Bearer {token}
```

---

## OpenAPI / API Documentation

Project dùng [Scramble](https://scramble.dedoc.co/) để tự động generate OpenAPI 3.1 spec từ source code — không cần viết annotation thủ công cho từng endpoint.

---

### Cách Scramble hoạt động

Scramble phân tích static code của controller để suy ra:

| Nguồn | Scramble suy ra |
|---|---|
| Return type hint / `@response` PHPDoc | Response schema |
| `FormRequest::rules()` | Request body schema + validation rules |
| Route parameters | Path parameters |
| `abort()`, exception `@throws` | Error responses (404, 403, 422...) |
| `->additional([...])` trên Resource | Extra top-level fields trong response |
| `LengthAwarePaginator` | Paginated response với `data`, `meta`, `links` |

**Không cần** viết `@OA\Get`, `@OA\Post` hay bất kỳ annotation OpenAPI nào.

---

### Annotate đúng cách

**Paginated list endpoint** — dùng `@response` để Scramble biết đây là paginated collection:

```php
/**
 * @response AnonymousResourceCollection<LengthAwarePaginator<TaskResource>>
 */
public function index(Request $request): AnonymousResourceCollection
{
    return TaskResource::collection(
        $this->repository->paginateForUser($request->user()->id, $request->only([...]))
    );
}
```

**Resource nằm ngoài `App\Models`** — thêm `@mixin` để Scramble tìm đúng model:

```php
use App\Domains\Task\Models\Task;

/**
 * @mixin Task
 */
final class TaskResource extends JsonResource
{
    public function toArray(Request $request): array { ... }
}
```

**Thêm mô tả cho field** — dùng PHPDoc inline trong `toArray()`:

```php
return [
    'id'       => $this->id,
    /**
     * Trạng thái hiện tại của task.
     * @example "todo"
     */
    'status'   => $this->status,
    /** @format date-time */
    'due_date' => $this->due_date?->toIso8601String(),
];
```

**Document error responses** — thêm `@throws` để Scramble tự thêm response 404/403:

```php
/**
 * @throws \Illuminate\Auth\Access\AuthorizationException
 */
public function show(Request $request, int $task): JsonResponse { ... }
```

**Override response type thủ công** khi cần:

```php
/**
 * @response TaskResource
 * @status 201
 */
public function store(CreateTaskRequest $request): JsonResponse { ... }
```

---

### Xem docs UI (local)

Scramble tích hợp sẵn Stoplight Elements UI tại:

```
http://localhost:8000/docs/api
```

> UI này chỉ accessible trong môi trường `local` theo mặc định (`RestrictedDocsAccess` middleware trong `config/scramble.php`).

---

### Export spec ra file

```bash
# Export minified
php artisan api:export

# Export pretty-print (dễ đọc, dễ diff trên git)
php artisan api:export --pretty
```

File được lưu tại `storage/api-docs/openapi.json` (đã gitignore — không commit file này).

Nên chạy lại sau mỗi khi thêm/sửa endpoint để giữ spec luôn up-to-date.

---

### Truy cập spec qua API (production)

Endpoint `/api/v1/documentation/openapi.json` được bảo vệ bằng **HMAC-SHA256** — chỉ client có API key hợp lệ mới lấy được spec.

**Bước 1 — Tạo API key**

```bash
php artisan api:generate-key postman
```

Output:

```
Field   Value
Name    postman
Key     ak_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Secret  xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

> Lưu `Secret` ngay — không thể xem lại sau này.

**Bước 2 — Tính signature**

```
string_to_sign = "{METHOD}\n{PATH}\n{TIMESTAMP}\n{BODY}"
signature      = HMAC-SHA256(string_to_sign, secret)
```

Ví dụ PHP:

```php
$method    = 'GET';
$path      = 'api/v1/documentation/openapi.json';
$timestamp = time();
$body      = '';

$signature = hash_hmac(
    'sha256',
    implode("\n", [$method, $path, $timestamp, $body]),
    $secret,
);
```

Ví dụ JavaScript (Node.js):

```js
const crypto = require('crypto');

const method    = 'GET';
const path      = 'api/v1/documentation/openapi.json';
const timestamp = Math.floor(Date.now() / 1000);
const body      = '';

const stringToSign = [method, path, timestamp, body].join('\n');
const signature    = crypto
    .createHmac('sha256', secret)
    .update(stringToSign)
    .digest('hex');
```

**Bước 3 — Gọi endpoint**

```bash
curl https://your-domain.com/api/v1/documentation/openapi.json \
  -H "X-API-Key: ak_xxx" \
  -H "X-Timestamp: 1748000000" \
  -H "X-Signature: abc123..."
```

> Timestamp phải trong vòng **5 phút** so với server time — bảo vệ chống replay attack.

---

### Tích hợp với Postman / Insomnia

**Postman:**
1. Import → Link → nhập URL endpoint spec (với headers HMAC)
2. Hoặc export file `openapi.json` rồi import trực tiếp

**Insomnia:**
1. Import → From URL hoặc From File
2. Chọn file `storage/api-docs/openapi.json`

---

### Workflow phát triển

```
1. Thêm/sửa endpoint trong controller
        ↓
2. Chạy: php artisan api:export --pretty
        ↓
3. Kiểm tra diff của storage/api-docs/openapi.json
   (file này không commit nhưng dùng để review locally)
        ↓
4. Mở http://localhost:8000/docs/api để xem UI
```

Nếu muốn commit spec vào repo (ví dụ để CI/CD dùng), bỏ `storage/api-docs/openapi.json` khỏi `.gitignore`.
