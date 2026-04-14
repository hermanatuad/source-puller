<!doctype html>
<html lang="en" data-layout-mode="dark" data-body-image="none">

<head>

    <?php echo $this->render('title-meta', array('title' => 'Landing')); ?>

    <!--Swiper slider css-->
    <link href="/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?php echo $this->render('head-css'); ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <!-- end navbar -->
        <div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>


        <!-- start features -->
        <section class="section">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h3 class="mb-3 fw-semibold">Human Bamboo</h3>
                            <p class="text-muted mb-4">Patient Lifetime Data.</p>

                            <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                                <a href="site/signin" class="btn btn-primary waves-effect waves-light px-3 d-inline-flex align-items-center gap-2">
                                    <i class="ri-login-box-line fs-16"></i>
                                    <span>Sign In</span>
                                </a>
                                <a href="site/xml" class="btn btn-outline-info waves-effect px-3 d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer">
                                    <i class="ri-file-code-line fs-16"></i>
                                    <span>XML Viewer</span>
                                </a>
                                <a href="site/xml-editor" class="btn btn-outline-warning waves-effect px-3 d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer">
                                    <i class="ri-edit-box-line fs-16"></i>
                                    <span>XML Editor</span>
                                </a>
                                <a href="site/xml-new" class="btn btn-outline-success waves-effect px-3 d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer">
                                    <i class="ri-file-list-3-line fs-16"></i>
                                    <span>XML Buluh Bambu</span>
                                </a>
                                <a href="site/xsd-new" class="btn btn-outline-secondary waves-effect px-3 d-inline-flex align-items-center gap-2" target="_blank" rel="noopener noreferrer">
                                    <i class="ri-shield-check-line fs-16"></i>
                                    <span>XML Schema Buluh Bambu</span>
                                </a>
                            </div>

                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <!-- <div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-md-2 row-cols-1"> -->
                    <!-- landing txt -->
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end features -->



        <!--start back-to-top-->
        <button onclick="topFunction()" class="btn btn-danger btn-icon landing-back-top" id="back-to-top">
            <i class="ri-arrow-up-line"></i>
        </button>
        <!--end back-to-top-->

    </div>
    <!-- end layout wrapper -->


    <?php echo $this->render('vendor-scripts'); ?>

    <!--Swiper slider js-->
    <script src="/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="/js/pages/landing.init.js"></script>
</body>

</html>