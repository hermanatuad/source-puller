<?php

namespace app\controllers;

use app\models\User;
use app\models\UserSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [

                'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page');
                    },
                    'only' => ['create', 'update', 'view', 'index', 'delete'],
                    'rules' => [
                        [
                            'actions' => [
                                'create',
                                'update',
                                'view',
                                'index',
                                'delete'
                            ],
                            'allow' => true,
                            'roles' => ['creator', 'admin'],
                        ]
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // Handle RBAC role assignment for new user
                $rbacRole = Yii::$app->request->post('rbac_role');
                if ($rbacRole) {
                    $auth = Yii::$app->authManager;
                    $role = $auth->getRole($rbacRole);
                    if ($role) {
                        $auth->assign($role, $model->id);
                        Yii::$app->session->setFlash('success', 'User created successfully with role: ' . $rbacRole);
                    }
                } else {
                    Yii::$app->session->setFlash('success', 'User created successfully');
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        // Get all available RBAC roles
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        $rolesList = [];
        foreach ($roles as $role) {
            $rolesList[$role->name] = $role->name;
        }

        return $this->render('create', [
            'model' => $model,
            'rolesList' => $rolesList,
            'assignedRoles' => [],
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $auth = Yii::$app->authManager;

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // Handle RBAC role change
            $rbacRole = Yii::$app->request->post('rbac_role');
            if ($rbacRole) {
                // Revoke all existing roles
                $auth->revokeAll($model->id);

                // Assign new role
                $role = $auth->getRole($rbacRole);
                if ($role) {
                    $auth->assign($role, $model->id);
                    Yii::$app->session->setFlash('success', 'User updated successfully with role: ' . $rbacRole);
                }
            } else {
                Yii::$app->session->setFlash('success', 'User updated successfully');
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // Get all available RBAC roles
        $roles = $auth->getRoles();
        $rolesList = [];
        foreach ($roles as $role) {
            $rolesList[$role->name] = $role->name;
        }

        // Get user's assigned roles
        $assignedRoles = [];
        $userRoles = $auth->getRolesByUser($model->id);
        foreach ($userRoles as $role) {
            $assignedRoles[] = $role->name;
        }

        return $this->render('update', [
            'model' => $model,
            'rolesList' => $rolesList,
            'assignedRoles' => $assignedRoles,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('danger', 'User ' . $model->name . ' deleted successfully');
        return $this->redirect(['index']);
    }

    /**
     * Update current user's profile
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel(Yii::$app->user->id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Profile updated successfully');
            return $this->redirect(['profile']);
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
