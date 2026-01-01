<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * RBAC initialization and management commands
 */
class RbacController extends Controller
{
    /**
     * Initialize RBAC with basic roles and permissions
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Remove all existing roles and permissions
        $auth->removeAll();

        // Create permissions
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);

        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = 'Delete post';
        $auth->add($deletePost);

        $viewPost = $auth->createPermission('viewPost');
        $viewPost->description = 'View post';
        $auth->add($viewPost);

        // Create roles and assign permissions
        
        // Guest role - can only view
        $guest = $auth->createRole('guest');
        $guest->description = 'Guest user';
        $auth->add($guest);
        $auth->addChild($guest, $viewPost);

        // User role - can create and view
        $user = $auth->createRole('user');
        $user->description = 'Regular user';
        $auth->add($user);
        $auth->addChild($user, $guest);
        $auth->addChild($user, $createPost);

        // Moderator role - can update and delete
        $moderator = $auth->createRole('moderator');
        $moderator->description = 'Moderator';
        $auth->add($moderator);
        $auth->addChild($moderator, $user);
        $auth->addChild($moderator, $updatePost);

        // Admin role - can do everything
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);
        $auth->addChild($admin, $moderator);
        $auth->addChild($admin, $deletePost);

        // Assign roles to users
        // Assign admin role to user with ID = 1
        $auth->assign($admin, 1);

        // Assign user role to user with ID = 2
        $auth->assign($user, 2);

        $this->stdout("RBAC initialized successfully!\n", \yii\helpers\Console::FG_GREEN);
        $this->stdout("Roles created: guest, user, moderator, admin\n");
        $this->stdout("User ID 1 assigned to 'admin' role\n");
        $this->stdout("User ID 2 assigned to 'user' role\n");

        return ExitCode::OK;
    }

    /**
     * Assign a role to a user
     * @param int $userId User ID
     * @param string $roleName Role name
     */
    public function actionAssign($userId, $roleName)
    {
        $auth = Yii::$app->authManager;
        
        $role = $auth->getRole($roleName);
        if (!$role) {
            $this->stderr("Role '{$roleName}' not found!\n", \yii\helpers\Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $auth->assign($role, $userId);
        $this->stdout("Role '{$roleName}' assigned to user ID {$userId}\n", \yii\helpers\Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Revoke a role from a user
     * @param int $userId User ID
     * @param string $roleName Role name
     */
    public function actionRevoke($userId, $roleName)
    {
        $auth = Yii::$app->authManager;
        
        $role = $auth->getRole($roleName);
        if (!$role) {
            $this->stderr("Role '{$roleName}' not found!\n", \yii\helpers\Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $auth->revoke($role, $userId);
        $this->stdout("Role '{$roleName}' revoked from user ID {$userId}\n", \yii\helpers\Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * List all roles and permissions
     */
    public function actionList()
    {
        $auth = Yii::$app->authManager;

        $this->stdout("\n=== Roles ===\n", \yii\helpers\Console::FG_YELLOW);
        $roles = $auth->getRoles();
        foreach ($roles as $role) {
            $this->stdout("- {$role->name}: {$role->description}\n");
        }

        $this->stdout("\n=== Permissions ===\n", \yii\helpers\Console::FG_YELLOW);
        $permissions = $auth->getPermissions();
        foreach ($permissions as $permission) {
            $this->stdout("- {$permission->name}: {$permission->description}\n");
        }

        return ExitCode::OK;
    }

    /**
     * Show user roles and permissions
     * @param int $userId User ID
     */
    public function actionShow($userId)
    {
        $auth = Yii::$app->authManager;

        $this->stdout("\n=== Roles for User ID {$userId} ===\n", \yii\helpers\Console::FG_YELLOW);
        $roles = $auth->getRolesByUser($userId);
        foreach ($roles as $role) {
            $this->stdout("- {$role->name}: {$role->description}\n");
        }

        $this->stdout("\n=== Permissions for User ID {$userId} ===\n", \yii\helpers\Console::FG_YELLOW);
        $permissions = $auth->getPermissionsByUser($userId);
        foreach ($permissions as $permission) {
            $this->stdout("- {$permission->name}: {$permission->description}\n");
        }

        return ExitCode::OK;
    }
}
