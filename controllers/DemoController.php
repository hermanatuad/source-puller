<?php

namespace app\controllers;

use app\models\Hospital;
use app\models\HospitalSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\helpers\DWHelper;
use app\models\Entity;
use app\models\EntityAffiliation;
use app\models\EntitySystem;

/**
 * DemoController implements the CRUD actions for Hospital model.
 */
class DemoController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-datawarehouse' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Hospital models.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDeleteDatawarehouse()
    {


        $request = \Yii::$app->request;

        if ($request->isPost) {
            $confirm = $request->post('confirm');

            if ($confirm !== 'YES_TRUNCATE_DW') {
                \Yii::$app->session->setFlash('error', 'Confirmation missing. POST with `confirm=YES_TRUNCATE_DW` to proceed.');
                return $this->redirect(['index']);
            }

            $dwConfig = DWHelper::getConfig();
            if (empty($dwConfig) || empty($dwConfig['dbname'])) {
                \Yii::$app->session->setFlash('error', 'Datawarehouse configuration is missing.');
                return $this->redirect(['index']);
            }

            try {
                $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;', $dwConfig['host'] ?? 'localhost', $dwConfig['port'] ?? 5432, $dwConfig['dbname']);
                $pdo = new \PDO($dsn, $dwConfig['username'] ?? null, $dwConfig['password'] ?? null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

                $tables = [];
                $stmt = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public'");
                if ($stmt) {
                    $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                }

                $pdo->exec('SET session_replication_role = replica;');
                foreach ($tables as $table) {
                    // Quote identifier
                    $tableQuoted = str_replace('"', '""', $table);
                    $pdo->exec("TRUNCATE TABLE \"{$tableQuoted}\" CASCADE;");
                }
                $pdo->exec('SET session_replication_role = origin;');

                \Yii::$app->session->setFlash('success', 'Datawarehouse truncated successfully.');
            } catch (\Throwable $e) {
                \Yii::$app->session->setFlash('error', 'Truncate failed: ' . $e->getMessage());
            }


            $entitySystem = EntitySystem::find()->all();
            try {
                foreach ($entitySystem as $es) {
                    $es->delete();
                }

                \Yii::$app->session->setFlash('success', 'Entity systems deleted successfully.');
            } catch (\Exception $e) {
                \Yii::$app->session->setFlash('error', 'Failed to delete entity systems.');
            }
            $entityAffiliation = EntityAffiliation::find()->all();
            try {
                foreach ($entityAffiliation as $ea) {
                    $ea->delete();
                }
                \Yii::$app->session->setFlash('success', 'Entity affiliations deleted successfully.');
            } catch (\Exception $e) {
                \Yii::$app->session->setFlash('error', 'Failed to delete entity affiliations.');
            }
            $entity = Entity::find()->all();
            try {
                foreach ($entity as $e) {
                    $e->delete();
                }
                \Yii::$app->session->setFlash('success', 'Entities deleted successfully.');
            } catch (\Exception $e) {
                \Yii::$app->session->setFlash('error', 'Failed to delete entities.');
            }

            return $this->redirect(['index']);
        }

        return $this->render('index');
    }


    /**
     * Finds the Hospital model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Hospital the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Hospital::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
