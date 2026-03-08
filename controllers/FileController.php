<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FileController extends Controller
{
    /**
     * Serve the example PDF inline.
     * Accessible via /file/example
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionExample()
    {
        $file = Yii::getAlias('@webroot') . '/example.pdf';
        if (!file_exists($file)) {
            throw new NotFoundHttpException('Example PDF not found.');
        }

        return Yii::$app->response->sendFile($file, 'example.pdf', [
            'mimeType' => 'application/pdf',
            'inline' => true,
        ]);
    }

    /**
     * Generic serve action that ignores requested path and returns example.pdf.
     * Useful for mapping external links to a local example file.
     * Accessible via /file/serve?name=anything or /file/serve
     *
     * @param string|null $name
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionServe($name = null)
    {
        $file = Yii::getAlias('@webroot') . '/example.pdf';
        if (!file_exists($file)) {
            throw new NotFoundHttpException('Example PDF not found.');
        }

        $downloadName = $name ? preg_replace('/[^A-Za-z0-9._-]/', '_', $name) : 'example.pdf';

        return Yii::$app->response->sendFile($file, $downloadName, [
            'mimeType' => 'application/pdf',
            'inline' => true,
        ]);
    }
}
