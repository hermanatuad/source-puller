<!doctype html>
<?php echo $this->render('setting'); ?>

<head>

    <?php echo $this->render('title-meta', array('title' => 'Sign Up')); ?>

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
                        <div class="card overflow-hidden m-0 card-bg-fill border-0 card-border-effect-none">
                            <div class="row justify-content-center g-0">
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

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Register Account</h5>
                                            <p class="text-muted">Get your free account now.</p>
                                        </div>

                                        <?php if (Yii::$app->session->hasFlash('success')): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <?= Yii::$app->session->getFlash('success') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-4">
                                            <?php

                                            use richardfan\widget\JSRegister;
                                            use yii\helpers\Html;
                                            use yii\widgets\ActiveForm;

                                            $form = ActiveForm::begin([
                                                'id' => 'signup-form',
                                                'options' => ['class' => 'needs-validation'],
                                                'enableClientValidation' => true,
                                            ]);
                                            ?>

                                            <div class="mb-3">
                                                <?= $form->field($model, 'email')->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Enter email address',
                                                    'type' => 'email',
                                                ])->label('Email <span class="text-danger">*</span>') ?>
                                            </div>

                                            <div class="mb-3">
                                                <?= $form->field($model, 'username')->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Enter username',
                                                ])->label('Username <span class="text-danger">*</span>') ?>
                                            </div>

                                            <div class="mb-3">
                                                <?= $form->field($model, 'name')->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Enter your full name (optional)',
                                                ])->label('Full Name <span class="text-danger">*</span>') ?>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                                <div class="position-relative auth-pass-inputgroup">
                                                    <?= $form->field($model, 'password', [
                                                        'template' => '{input}{error}',
                                                        'options' => ['class' => '']
                                                    ])->passwordInput([
                                                        'class' => 'form-control pe-5 password-input',
                                                        'placeholder' => 'Enter password',
                                                        'id' => 'password-input',
                                                    ]) ?>
                                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon">
                                                        <i class="ri-eye-fill align-middle"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <p class="mb-0 fs-12 text-muted fst-italic">By registering you agree to our Terms of Use</p>
                                            </div>

                                            <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                <h5 class="fs-13">Password must contain:</h5>
                                                <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                                <p id="pass-lower" class="invalid fs-12 mb-2">At least <b>lowercase</b> letter (a-z)</p>
                                                <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                <p id="pass-number" class="invalid fs-12 mb-0">At least <b>number</b> (0-9)</p>
                                            </div>

                                            <div class="mt-4">
                                                <?= Html::submitButton('Sign Up', ['class' => 'btn btn-primary w-100']) ?>
                                            </div>

                                            <?php ActiveForm::end(); ?>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Already have an account? <?= Html::a('Sign in', ['site/signin'], ['class' => 'fw-semibold text-primary text-decoration-underline']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

    <!-- password create init -->
    <script src="/js/pages/passowrd-create.init.js"></script>
    <?php JSRegister::begin(); ?>
    <script>
        // Password toggle
        document.getElementById('password-addon').addEventListener('click', function() {
            var passwordInput = document.getElementById('password-input');
            var icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ri-eye-fill');
                icon.classList.add('ri-eye-off-fill');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ri-eye-off-fill');
                icon.classList.add('ri-eye-fill');
            }
        });
    </script>
    <?php JSRegister::end(); ?>
</body>

</html>