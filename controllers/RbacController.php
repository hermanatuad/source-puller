<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\rbac\models\AuthItem;
use app\rbac\models\AuthAssignment;

class RbacController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['creator'], // Only creator can access RBAC management
                    ],
                ],
            ],
        ];
    }

    /**
     * RBAC Management Dashboard
     */
    public function actionIndex()
    {
        $this->view->title = 'RBAC Management';
        $this->view->params['pagetitle'] = 'Management';
        $this->view->params['title'] = 'RBAC Management';

        $authManager = Yii::$app->authManager;
        
        // Get all roles
        $roles = $authManager->getRoles();
        
        // Get all permissions
        $permissions = $authManager->getPermissions();
        
        // Get all users with their roles
        $users = User::find()->all();
        $userRoles = [];
        foreach ($users as $user) {
            $userRoles[$user->id] = $authManager->getRolesByUser($user->id);
        }

        return $this->render('index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'users' => $users,
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Assign role to user
     */
    public function actionAssign()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->request->post('user_id');
            $roleName = Yii::$app->request->post('role_name');
            
            $authManager = Yii::$app->authManager;
            $role = $authManager->getRole($roleName);
            
            if ($role && $userId) {
                // Revoke all existing roles first
                $authManager->revokeAll($userId);
                
                // Assign new role
                $authManager->assign($role, $userId);
                
                Yii::$app->session->setFlash('success', "Role '$roleName' has been assigned to user.");
            } else {
                Yii::$app->session->setFlash('error', 'Invalid role or user.');
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Revoke role from user
     */
    public function actionRevoke()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->request->post('user_id');
            $roleName = Yii::$app->request->post('role_name');
            
            $authManager = Yii::$app->authManager;
            $role = $authManager->getRole($roleName);
            
            if ($role && $userId) {
                $authManager->revoke($role, $userId);
                Yii::$app->session->setFlash('success', "Role '$roleName' has been revoked from user.");
            } else {
                Yii::$app->session->setFlash('error', 'Invalid role or user.');
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * View role details and permissions
     */
    public function actionViewRole($name)
    {
        $this->view->title = 'Role: ' . $name;
        $this->view->params['pagetitle'] = 'RBAC';
        $this->view->params['title'] = 'Role Details';

        $authManager = Yii::$app->authManager;
        $role = $authManager->getRole($name);
        
        if (!$role) {
            throw new \yii\web\NotFoundHttpException('Role not found.');
        }
        
        // Get permissions for this role
        $permissions = $authManager->getPermissionsByRole($name);
        
        // Get child roles
        $children = $authManager->getChildren($name);
        
        // Get users with this role
        $userIds = $authManager->getUserIdsByRole($name);
        $users = User::find()->where(['id' => $userIds])->all();

        return $this->render('view-role', [
            'role' => $role,
            'permissions' => $permissions,
            'children' => $children,
            'users' => $users,
        ]);
    }

    /**
     * View user's roles and permissions
     */
    public function actionViewUser($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new \yii\web\NotFoundHttpException('User not found.');
        }

        $this->view->title = 'User: ' . $user->username;
        $this->view->params['pagetitle'] = 'RBAC';
        $this->view->params['title'] = 'User Permissions';

        $authManager = Yii::$app->authManager;
        
        // Get user's roles
        $roles = $authManager->getRolesByUser($id);
        
        // Get user's permissions
        $permissions = $authManager->getPermissionsByUser($id);

        return $this->render('view-user', [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}
