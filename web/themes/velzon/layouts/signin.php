<!doctype html>
<?php echo $this->render('setting'); ?>

<head>

    <?php 
    use yii\bootstrap5\ActiveForm;
    use yii\bootstrap5\Html;
    
    echo $this->render('title-meta', array('title' => 'Sign In')); 
    ?>

    <?php echo $this->render('head-css'); ?>

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden card-bg-fill border-0 card-border-effect-none">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="/" class="d-block">
                                                    <img src="/images/logo-light.png" alt="" height="18">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" The theme is really great with an amazing customer support."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p class="text-muted">Sign in to continue to Velzon.</p>
                                        </div>

                                        <div class="mt-4">
                                            <?php $form = ActiveForm::begin([
                                                'id' => 'login-form',
                                                'options' => ['class' => ''],
                                            ]); ?>

                                            <div class="mb-3">
                                                <?= $form->field($model, 'username', [
                                                    'template' => '{label}{input}{error}',
                                                    'options' => ['class' => ''],
                                                ])->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Enter username',
                                                    'autofocus' => true
                                                ])->label('Username', ['class' => 'form-label']) ?>
                                            </div>

                                            <div class="mb-3">
                                                <div class="float-end">
                                                    <a href="#" class="text-muted">Forgot password?</a>
                                                </div>
                                                <?= $form->field($model, 'password', [
                                                    'template' => '{label}<div class="position-relative auth-pass-inputgroup">{input}<button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button"><i class="ri-eye-fill align-middle"></i></button></div>{error}',
                                                    'options' => ['class' => ''],
                                                ])->passwordInput([
                                                    'class' => 'form-control pe-5 password-input',
                                                    'placeholder' => 'Enter password'
                                                ])->label('Password', ['class' => 'form-label']) ?>
                                            </div>

                                            <div class="form-check mb-3">
                                                <?= $form->field($model, 'rememberMe', [
                                                    'template' => '{input}{label}{error}',
                                                    'options' => ['class' => ''],
                                                ])->checkbox([
                                                    'class' => 'form-check-input',
                                                    'id' => 'auth-remember-check',
                                                    'label' => 'Remember me',
                                                    'labelOptions' => ['class' => 'form-check-label'],
                                                ], false) ?>
                                            </div>

                                            <div class="mt-4">
                                                <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
                                            </div>

                                            <?php ActiveForm::end(); ?>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title">Or sign in with</h5>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-soft-success w-100">
                                                        <i class="ri-whatsapp-line fs-16 align-middle me-2"></i>
                                                        <span class="align-middle">WhatsApp</span>
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-soft-danger w-100">
                                                        <i class="ri-google-fill fs-16 align-middle me-2"></i>
                                                        <span class="align-middle">Google</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-soft-info w-100">
                                                        <i class="ri-telegram-line fs-16 align-middle me-2"></i>
                                                        <span class="align-middle">Telegram</span>
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-soft-dark w-100">
                                                        <i class="ri-github-fill fs-16 align-middle me-2"></i>
                                                        <span class="align-middle">GitHub</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Don't have an account? <?= \yii\helpers\Html::a('Sign up', ['site/signup'], ['class' => 'fw-semibold text-primary text-decoration-underline']) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> Velzon. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <?php echo $this->render('vendor-scripts'); ?>

    <!-- password-addon init -->
    <script src="/js/pages/password-addon.init.js"></script>
</body>

</html>