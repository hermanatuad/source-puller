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
        $viewPost = $auth->createPermission('viewPost');
        $viewPost->description = 'View post';
        $auth->add($viewPost);

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);

        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = 'Delete post';
        $auth->add($deletePost);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Manage users';
        $auth->add($manageUsers);

        $manageRoles = $auth->createPermission('manageRoles');
        $manageRoles->description = 'Manage roles and permissions';
        $auth->add($manageRoles);

        $systemSettings = $auth->createPermission('systemSettings');
        $systemSettings->description = 'Access system settings';
        $auth->add($systemSettings);

        // Create roles with hierarchy
        
        // User role - lowest level, can view and create
        $user = $auth->createRole('user');
        $user->description = 'Regular user - can view and create content';
        $auth->add($user);
        $auth->addChild($user, $viewPost);
        $auth->addChild($user, $createPost);

        // Admin role - middle level, can manage content and users
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator - can manage content and users';
        $auth->add($admin);
        $auth->addChild($admin, $user);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $deletePost);
        $auth->addChild($admin, $manageUsers);

        // Creator role - highest level, full system access
        $creator = $auth->createRole('creator');
        $creator->description = 'Creator - full system access';
        $auth->add($creator);
        $auth->addChild($creator, $admin);
        $auth->addChild($creator, $manageRoles);
        $auth->addChild($creator, $systemSettings);

        // Assign roles to users
        // User ID 1 = creator
        $auth->assign($creator, 1);
        // User ID 2 = admin
        $auth->assign($admin, 2);
        // User ID 3 = user
        $auth->assign($user, 3);

        $this->stdout("RBAC initialized successfully!\n", \yii\helpers\Console::FG_GREEN);
        $this->stdout("\nRole Hierarchy:\n", \yii\helpers\Console::FG_YELLOW);
        $this->stdout("  creator (highest) -> admin -> user (lowest)\n");
        $this->stdout("\nRole Assignments:\n", \yii\helpers\Console::FG_YELLOW);
        $this->stdout("  User ID 1 -> 'creator' role\n");
        $this->stdout("  User ID 2 -> 'admin' role\n");
        $this->stdout("  User ID 3 -> 'user' role\n");

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
