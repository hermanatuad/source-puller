# Setup Login & RBAC

## Setup Database

1. Configure database connection di `.env` atau update `config/db.php`:
```php
'dsn' => 'mysql:host=localhost;port=3306;dbname=yii2basic',
'username' => 'root',
'password' => '',
```

2. Jalankan migrations:
```bash
./yii migrate
```

Migrations yang akan dijalankan:
- `m260101_000001_create_users_table` - Membuat tabel users
- `m260101_000002_init_rbac` - Membuat tabel RBAC (auth_rule, auth_item, auth_item_child, auth_assignment)
- `m260101_000003_seed_users` - Menambahkan user admin dan demo

## Initialize RBAC

Setelah migrations selesai, initialize RBAC dengan menjalankan:

```bash
./yii rbac/init
```

Ini akan membuat:
- **Roles**: guest, user, moderator, admin
- **Permissions**: viewPost, createPost, updatePost, deletePost
- Role assignment:
  - User ID 1 (admin) → admin role
  - User ID 2 (demo) → user role

## User Login

### Default Users:

**Admin:**
- Username: `admin`
- Password: `admin123`
- Role: admin

**Demo:**
- Username: `demo`
- Password: `demo123`
- Role: user

### Login URL:
- `/site/login` atau `/login`

## RBAC Commands

### Assign role ke user:
```bash
./yii rbac/assign <userId> <roleName>

# Contoh:
./yii rbac/assign 1 admin
./yii rbac/assign 2 user
```

### Revoke role dari user:
```bash
./yii rbac/revoke <userId> <roleName>

# Contoh:
./yii rbac/revoke 2 user
```

### List semua roles dan permissions:
```bash
./yii rbac/list
```

### Show roles dan permissions untuk user tertentu:
```bash
./yii rbac/show <userId>

# Contoh:
./yii rbac/show 1
```

## Cara Menggunakan RBAC di Controller

### Check permission di controller action:

```php
public function actionCreate()
{
    if (!\Yii::$app->user->can('createPost')) {
        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
    }
    
    // Your code here
}
```

### Check role:

```php
// Check apakah user adalah admin
if (\Yii::$app->user->can('admin')) {
    // Admin-only code
}
```

### Menggunakan access control filter:

```php
public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => ['@'], // Authenticated users
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['createPost'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['updatePost'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['admin'],
                ],
            ],
        ],
    ];
}
```

## User Model

User model sekarang menggunakan database dengan fitur:
- Password hashing (secure)
- Auth key untuk "remember me"
- Password reset token
- User status (active/inactive/deleted)
- Timestamps (created_at, updated_at)

### Membuat user baru secara programmatic:

```php
$user = new \app\models\User();
$user->username = 'newuser';
$user->email = 'newuser@example.com';
$user->setPassword('password123');
$user->generateAuthKey();
$user->status = \app\models\User::STATUS_ACTIVE;

if ($user->save()) {
    // Assign role
    $auth = Yii::$app->authManager;
    $userRole = $auth->getRole('user');
    $auth->assign($userRole, $user->id);
}
```

## Helper untuk Check Permission di View

Gunakan helper MyHelper:

```php
use app\helpers\MyHelper;

// Di view file
<?php if (MyHelper::can('createPost')): ?>
    <?= Html::a('Create Post', ['post/create']) ?>
<?php endif; ?>

<?php if (MyHelper::can(['admin', 'moderator'])): ?>
    <?= Html::a('Admin Panel', ['/admin']) ?>
<?php endif; ?>
```

## Role Hierarchy

```
admin (can do everything)
  └─ moderator (can update posts)
      └─ user (can create and view posts)
          └─ guest (can only view posts)
```

## Troubleshooting

### Database connection error
Pastikan database sudah dibuat dan config di `config/db.php` benar.

### Migration error
Pastikan semua migrations dijalankan dengan:
```bash
./yii migrate
```

### RBAC not working
1. Pastikan authManager sudah dikonfigurasi di `config/web.php` dan `config/console.php`
2. Jalankan `./yii rbac/init` untuk initialize roles dan permissions
3. Check assignment dengan `./yii rbac/show <userId>`
