<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'xml-editor' => ['get', 'post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays error page with custom layouts
     *
     * @return string
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $statusCode = $exception->statusCode ?? 500;
            
            if ($statusCode == 403) {
                return $this->render('error', ['exception' => $exception]);
            }
            
            $this->layout = false;
            
            if ($statusCode == 404) {
                return $this->render('@webroot/themes/velzon/layouts/404');
            } elseif ($statusCode == 500 || $statusCode >= 500) {
                return $this->render('@webroot/themes/velzon/layouts/500');
            }
            
            return $this->render('error', ['exception' => $exception]);
        }
        
        return $this->render('error');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            // Not logged in - show landing page
            $this->layout = false;
            return $this->render('@webroot/themes/velzon/layouts/landing');
        }
        
        // Logged in - show dashboard
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionSignin()
    {
        $this->layout = false;
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('@webroot/themes/velzon/layouts/signin', [
            'model' => $model,
        ]);
    }

    /**
     * Signup action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $this->layout = false;
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new \app\models\SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please login with your credentials.');
            return $this->redirect(['signin']);
        }

        return $this->render('@webroot/themes/velzon/layouts/signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionTest()
    {
        return $this->render('test');
    }

    /**
     * Serve raw patient XML file at /site/xml
     *
     * @return string
     */
    public function actionXml()
    {
        $xmlPath = Yii::getAlias('@webroot') . '/patient-new.xml';
        if (!file_exists($xmlPath)) {
            throw new NotFoundHttpException('patient-new.xml not found');
        }

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return file_get_contents($xmlPath);
    }

    public function actionXmlNew()
    {
        $xmlPath = Yii::getAlias('@webroot') . '/patient-new.xml';
        if (!file_exists($xmlPath)) {
            throw new NotFoundHttpException('patient-new.xml not found');
        }

        $this->view->title = 'Patient New XML Viewer';
        $this->view->params['pagetitle'] = 'Data Sources';
        $this->view->params['title'] = 'Patient New XML Viewer';
        $this->view->params['breadcrumbs'] = [
            ['label' => 'Data Sources', 'url' => ['system/index']],
            'Patient New XML Viewer',
        ];

        $xmlContent = file_get_contents($xmlPath);
        $dom = null;
        $parseErrors = [];

        $previousUseInternalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        if ($dom->loadXML($xmlContent, LIBXML_NOBLANKS)) {
            $xmlContent = $dom->saveXML();
        } else {
            $parseErrors = array_map(static function (\LibXMLError $error) {
                return sprintf(
                    'Line %d, column %d: %s',
                    $error->line,
                    $error->column,
                    trim($error->message)
                );
            }, libxml_get_errors());
            $dom = null;
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previousUseInternalErrors);

        clearstatcache(true, $xmlPath);

        return $this->render('xml-new', [
            'xmlPath' => $xmlPath,
            'xmlContent' => $xmlContent,
            'dom' => $dom,
            'parseErrors' => $parseErrors,
            'lastModifiedAt' => filemtime($xmlPath) ?: null,
        ]);

    }

    public function actionXsdNew()
    {
        $xsdPath = Yii::getAlias('@webroot') . '/patient-new.xsd';
        if (!file_exists($xsdPath)) {
            throw new NotFoundHttpException('patient-new.xsd not found');
        }

        $this->view->title = 'Patient New XSD Viewer';
        $this->view->params['pagetitle'] = 'Data Sources';
        $this->view->params['title'] = 'Patient New XSD Viewer';
        $this->view->params['breadcrumbs'] = [
            ['label' => 'Data Sources', 'url' => ['system/index']],
            'Patient New XSD Viewer',
        ];

        $xsdContent = file_get_contents($xsdPath);
        clearstatcache(true, $xsdPath);

        return $this->render('xsd-new', [
            'xsdPath' => $xsdPath,
            'xsdContent' => $xsdContent,
            'lastModifiedAt' => filemtime($xsdPath) ?: null,
        ]);
    }

    /**
     * Displays and updates patient.xml from a single page editor.
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionXmlEditor()
    {
        $xmlPath = Yii::getAlias('@webroot') . '/patient.xml';
        if (!file_exists($xmlPath)) {
            throw new NotFoundHttpException('patient.xml not found');
        }

        $this->view->title = 'Patient XML Editor';
        $this->view->params['pagetitle'] = 'Data Sources';
        $this->view->params['title'] = 'Patient XML Editor';
        $this->view->params['breadcrumbs'] = [
            ['label' => 'Data Sources', 'url' => ['system/index']],
            'Patient XML Editor',
        ];

        $xmlContent = file_get_contents($xmlPath);
        $request = Yii::$app->request;

        if ($request->isPost) {
            $xmlContent = (string) $request->post('xml_content', '');

            if (trim($xmlContent) === '') {
                Yii::$app->session->setFlash('error', 'XML content cannot be empty.');
            } else {
                $previousUseInternalErrors = libxml_use_internal_errors(true);
                libxml_clear_errors();

                $dom = new \DOMDocument('1.0', 'UTF-8');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;

                if ($dom->loadXML($xmlContent, LIBXML_NOBLANKS)) {
                    $formattedXml = $dom->saveXML();

                    if (file_put_contents($xmlPath, $formattedXml, LOCK_EX) === false) {
                        Yii::$app->session->setFlash('error', 'Failed to save patient.xml. Please check file permissions.');
                    } else {
                        Yii::$app->session->setFlash('success', 'patient.xml updated successfully.');
                        libxml_clear_errors();
                        libxml_use_internal_errors($previousUseInternalErrors);

                        return $this->redirect(['xml-editor']);
                    }
                } else {
                    $errors = array_map(static function (\LibXMLError $error) {
                        return sprintf(
                            'Line %d, column %d: %s',
                            $error->line,
                            $error->column,
                            trim($error->message)
                        );
                    }, libxml_get_errors());

                    Yii::$app->session->setFlash('error', 'XML is invalid. ' . implode(' | ', $errors));
                }

                libxml_clear_errors();
                libxml_use_internal_errors($previousUseInternalErrors);
            }
        }

        clearstatcache(true, $xmlPath);

        return $this->render('xml-editor', [
            'xmlContent' => $xmlContent,
            'xmlPath' => $xmlPath,
            'lastModifiedAt' => filemtime($xmlPath) ?: null,
        ]);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
