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

                            <a href="site/signin" class="btn btn-link fw-medium text-decoration-none text-body">Sign in</a>
                            <a href="site/xml" class="btn btn-link fw-medium text-decoration-none text-body" target="_blank">XML File</a>

                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <!-- <div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-md-2 row-cols-1"> -->
                <div class="row row-cols-xxl-12 row-cols-lg-12 row-cols-md-12 row-cols-12">
                    <?php
                    $xmlPath = __DIR__ . '/../../../patient.xml';
                    $patient = null;
                    if (file_exists($xmlPath)) {
                        libxml_use_internal_errors(true);
                        $patient = simplexml_load_file($xmlPath);
                    }
                    ?>

                    <div class="col">
                        <?php if ($patient): ?>
                            <?php $pid = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$patient['id']); ?>
                            <div class="accordion" id="patientAccordion-<?php echo $pid; ?>">
                                <div class="accordion-item">
                                    <h1 class="accordion-header" id="headingIdentity-<?php echo $pid; ?>">
                                        <button class="accordion-button fs-3 py-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIdentity-<?php echo $pid; ?>" aria-expanded="true" aria-controls="collapseIdentity-<?php echo $pid; ?>">
                                            Patient Identity
                                        </button>
                                    </h1>
                                    <div id="collapseIdentity-<?php echo $pid; ?>" class="accordion-collapse collapse show" aria-labelledby="headingIdentity-<?php echo $pid; ?>">
                                        <div class="accordion-body">
                                            <p class="mb-1"><strong>Patient ID:</strong> <?php echo htmlspecialchars((string)$patient['id']); ?></p>
                                            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars((string)$patient->identity->patientName); ?></p>
                                            <p class="mb-1"><strong>National IC:</strong> <?php echo htmlspecialchars((string)$patient->identity->patientNationalIC); ?></p>
                                            <p class="mb-0"><strong>Passport:</strong> <?php echo htmlspecialchars((string)$patient->identity->patientPassport); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingDemo-<?php echo $pid; ?>">
                                        <button class="accordion-button fs-3 py-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDemo-<?php echo $pid; ?>" aria-expanded="false" aria-controls="collapseDemo-<?php echo $pid; ?>">
                                            Demographic
                                        </button>
                                    </h2>
                                    <div id="collapseDemo-<?php echo $pid; ?>" class="accordion-collapse collapse" aria-labelledby="headingDemo-<?php echo $pid; ?>">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <p class="mb-1"><strong>Date of Birth:</strong> <?php echo htmlspecialchars((string)$patient->demographic->dateOfBirth); ?></p>
                                                    <p class="mb-1"><strong>Gender:</strong> <?php echo htmlspecialchars((string)$patient->demographic->gender); ?></p>
                                                    <p class="mb-1"><strong>Race:</strong> <?php echo htmlspecialchars((string)$patient->demographic->race); ?></p>
                                                    <p class="mb-1"><strong>Nationality:</strong> <?php echo htmlspecialchars((string)$patient->demographic->nationality); ?></p>
                                                    <p class="mb-1"><strong>Address:</strong> <?php echo htmlspecialchars((string)$patient->demographic->address); ?></p>
                                                    <p class="mb-1"><strong>Contact:</strong> <?php echo htmlspecialchars((string)$patient->demographic->contact); ?></p>
                                                    <p class="mb-0"><strong>Language:</strong> <?php echo htmlspecialchars((string)$patient->demographic->preferredLanguage); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingChecks-<?php echo $pid; ?>">
                                        <button class="accordion-button fs-3 py-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChecks-<?php echo $pid; ?>" aria-expanded="false" aria-controls="collapseChecks-<?php echo $pid; ?>">
                                            Clinical Checks (<?php echo count($patient->clinicals->clinicalCheck); ?>)
                                        </button>
                                    </h2>
                                    <div id="collapseChecks-<?php echo $pid; ?>" class="accordion-collapse collapse" aria-labelledby="headingChecks-<?php echo $pid; ?>">
                                        <div class="accordion-body">
                                            <div class="accordion" id="checksAccordion-<?php echo $pid; ?>">
                                                <?php foreach ($patient->clinicals->clinicalCheck as $check): $cid = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$check['id']); ?>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="checkHeading-<?php echo $pid . '-' . $cid; ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#checkCollapse-<?php echo $pid . '-' . $cid; ?>" aria-expanded="false" aria-controls="checkCollapse-<?php echo $pid . '-' . $cid; ?>">
                                                                Check #<?php echo htmlspecialchars((string)$check['id']); ?> — <?php echo htmlspecialchars((string)$check->dateTime); ?>
                                                            </button>
                                                        </h2>
                                                        <div id="checkCollapse-<?php echo $pid . '-' . $cid; ?>" class="accordion-collapse collapse" aria-labelledby="checkHeading-<?php echo $pid . '-' . $cid; ?>">
                                                            <div class="accordion-body">
                                                                <p class="mb-1"><strong>Weight:</strong> <?php echo htmlspecialchars((string)$check->weight); ?> <?php echo htmlspecialchars((string)$check->weight['unit']); ?></p>
                                                                <p class="mb-1"><strong>Height:</strong> <?php echo htmlspecialchars((string)$check->height); ?> <?php echo htmlspecialchars((string)$check->height['unit']); ?></p>
                                                                <p class="mb-0"><strong>BMI:</strong> <?php echo htmlspecialchars((string)$check->bmi); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingEps-<?php echo $pid; ?>">
                                        <button class="accordion-button fs-3 py-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEps-<?php echo $pid; ?>" aria-expanded="false" aria-controls="collapseEps-<?php echo $pid; ?>">
                                            Episodes (<?php echo count($patient->Episodes->episode); ?>)
                                        </button>
                                    </h2>
                                    <div id="collapseEps-<?php echo $pid; ?>" class="accordion-collapse collapse" aria-labelledby="headingEps-<?php echo $pid; ?>">
                                        <div class="accordion-body">
                                            <div class="accordion" id="epsAccordion-<?php echo $pid; ?>">
                                                <?php foreach ($patient->Episodes->episode as $ep): $eid = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$ep['id']); ?>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="epHeading-<?php echo $pid . '-' . $eid; ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#epCollapse-<?php echo $pid . '-' . $eid; ?>" aria-expanded="false" aria-controls="epCollapse-<?php echo $pid . '-' . $eid; ?>">
                                                                Episode <?php echo htmlspecialchars((string)$ep['id']); ?> — <?php echo htmlspecialchars((string)$ep['type']); ?>
                                                            </button>
                                                        </h2>
                                                        <div id="epCollapse-<?php echo $pid . '-' . $eid; ?>" class="accordion-collapse collapse" aria-labelledby="epHeading-<?php echo $pid . '-' . $eid; ?>">
                                                            <div class="accordion-body">
                                                                <?php foreach ($ep->Stages->stage as $st): $stid = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$st['id']); ?>
                                                                    <div class="card mb-2">
                                                                        <div class="card-body p-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <h6 class="mb-1">Stage: <?php echo htmlspecialchars((string)$st['type']); ?> <small class="text-muted">#<?php echo htmlspecialchars((string)$st['id']); ?></small></h6>
                                                                                    <?php if (isset($st['subtype']) && (string)$st['subtype'] !== ''): ?><p class="mb-1"><strong>Subtype:</strong> <?php echo htmlspecialchars((string)$st['subtype']); ?></p><?php endif; ?>
                                                                                    <?php if (isset($st->startDateTime) && (string)$st->startDateTime !== ''): ?><p class="mb-1"><strong>Start:</strong> <?php echo htmlspecialchars((string)$st->startDateTime); ?></p><?php endif; ?>
                                                                                    <?php if (isset($st->endDateTime) && (string)$st->endDateTime !== ''): ?><p class="mb-1"><strong>End:</strong> <?php echo htmlspecialchars((string)$st->endDateTime); ?></p><?php endif; ?>
                                                                                    <?php if (isset($st->dateTime) && (string)$st->dateTime !== ''): ?><p class="mb-1"><strong>Date:</strong> <?php echo htmlspecialchars((string)$st->dateTime); ?></p><?php endif; ?>
                                                                                    <?php if (isset($st->description) && (string)$st->description !== ''): ?><p class="text-muted mb-1"><?php echo htmlspecialchars((string)$st->description); ?></p><?php endif; ?>
                                                                                </div>
                                                                                <div class="text-end">
                                                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#stageDetails-<?php echo $pid . '-' . $stid; ?>" aria-expanded="false" aria-controls="stageDetails-<?php echo $pid . '-' . $stid; ?>">Detail</button>
                                                                                </div>
                                                                            </div>

                                                                            <div class="collapse mt-2" id="stageDetails-<?php echo $pid . '-' . $stid; ?>">
                                                                                <?php if (isset($st->radiology->medicalImaging)): ?>
                                                                                    <div class="mb-2">
                                                                                        <h6 class="fs-14 mb-1">Radiology / Medical Imaging</h6>
                                                                                        <?php foreach ($st->radiology->medicalImaging as $mi): $miid = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$mi['id']); ?>
                                                                                            <div class="card mb-2">
                                                                                                <div class="card-body p-2">
                                                                                                    <p class="mb-1"><strong><?php echo htmlspecialchars((string)$mi['id']); ?></strong> &mdash; <?php echo htmlspecialchars((string)$mi->modality); ?> (<?php echo htmlspecialchars((string)$mi->bodyPart); ?>)</p>
                                                                                                    <p class="text-muted mb-1">Machine: <?php echo htmlspecialchars((string)$mi->machine); ?> &middot; Date: <?php echo htmlspecialchars((string)$mi->dateTime); ?></p>
                                                                                                    <?php if (isset($mi->dicomStudyUID) && (string)$mi->dicomStudyUID !== ''): ?><p class="text-muted mb-1">Study UID: <?php echo htmlspecialchars((string)$mi->dicomStudyUID); ?></p><?php endif; ?>

                                                                                                    <?php if (isset($mi->imageFiles->imageFile)): ?>
                                                                                                        <div class="mb-1">
                                                                                                            <strong>Files:</strong>
                                                                                                            <ul class="mb-0">
                                                                                                                <?php foreach ($mi->imageFiles->imageFile as $img): ?>
                                                                                                                    <li><a href="<?php echo htmlspecialchars((string)$img->link); ?>" target="_blank"><?php echo htmlspecialchars((string)$img->format); ?></a></li>
                                                                                                                <?php endforeach; ?>
                                                                                                            </ul>
                                                                                                        </div>
                                                                                                    <?php endif; ?>

                                                                                                    <?php if (isset($mi->radiologyReport)): ?>
                                                                                                        <div>
                                                                                                            <strong>Report:</strong>
                                                                                                            <?php if (isset($mi->radiologyReport->link) && (string)$mi->radiologyReport->link !== ''): ?>
                                                                                                                <a href="<?php echo htmlspecialchars((string)$mi->radiologyReport->link); ?>" target="_blank">Download (<?php echo htmlspecialchars((string)$mi->radiologyReport->format); ?>)</a>
                                                                                                            <?php endif; ?>
                                                                                                            <?php if (isset($mi->radiologyReport->summary) && trim((string)$mi->radiologyReport->summary) !== ''): ?>
                                                                                                                <p class="text-muted mb-0 mt-1"><?php echo nl2br(htmlspecialchars(trim((string)$mi->radiologyReport->summary))); ?></p>
                                                                                                            <?php endif; ?>
                                                                                                        </div>
                                                                                                    <?php endif; ?>
                                                                                                </div>
                                                                                            </div>
                                                                                        <?php endforeach; ?>
                                                                                    </div>
                                                                                <?php endif; ?>

                                                                                <?php if (isset($st->documents->document)): ?>
                                                                                    <div class="mb-2">
                                                                                        <h6 class="fs-14 mb-1">Documents</h6>
                                                                                        <ul class="list-unstyled mb-0">
                                                                                            <?php foreach ($st->documents->document as $doc): ?>
                                                                                                <li class="mb-1">
                                                                                                    <strong><?php echo htmlspecialchars((string)$doc->documentType); ?></strong>
                                                                                                    <div class="text-muted">Format: <?php echo htmlspecialchars((string)$doc->format); ?><?php if (isset($doc->createdDate) && (string)$doc->createdDate !== ''): ?> &middot; Created: <?php echo htmlspecialchars((string)$doc->createdDate); ?><?php endif; ?></div>
                                                                                                    <?php if (isset($doc->link) && trim((string)$doc->link) !== ''): ?><div><a href="<?php echo htmlspecialchars((string)$doc->link); ?>" target="_blank">Open document</a></div><?php endif; ?>
                                                                                                </li>
                                                                                            <?php endforeach; ?>
                                                                                        </ul>
                                                                                    </div>
                                                                                <?php endif; ?>

                                                                                <?php if (isset($st->checkLaboratory->laboratoryTest)): ?>
                                                                                    <div class="mb-2">
                                                                                        <h6 class="fs-14 mb-1">Laboratory Tests</h6>
                                                                                        <div class="table-responsive">
                                                                                            <table class="table table-sm mb-0">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Test</th>
                                                                                                        <th>Result</th>
                                                                                                        <th>Date</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    <?php foreach ($st->checkLaboratory->laboratoryTest as $lt): ?>
                                                                                                        <tr>
                                                                                                            <td><?php echo htmlspecialchars((string)$lt->testName); ?></td>
                                                                                                            <td><?php echo htmlspecialchars((string)$lt->result); ?></td>
                                                                                                            <td class="text-muted"><?php echo htmlspecialchars((string)$lt->dateTime); ?></td>
                                                                                                        </tr>
                                                                                                    <?php endforeach; ?>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php endif; ?>

                                                                                <?php if (isset($st->monitoring->vitalSign)): ?>
                                                                                    <div class="mb-2">
                                                                                        <h6 class="fs-14 mb-1">Monitoring - Vital Signs</h6>
                                                                                        <div class="table-responsive">
                                                                                            <table class="table table-sm mb-0">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>BP</th>
                                                                                                        <th>Heart Rate</th>
                                                                                                        <th>Date</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    <?php foreach ($st->monitoring->vitalSign as $vs): ?>
                                                                                                        <tr>
                                                                                                            <td><?php echo htmlspecialchars((string)$vs->bloodPressure); ?></td>
                                                                                                            <td><?php echo htmlspecialchars((string)$vs->heartRate); ?></td>
                                                                                                            <td class="text-muted"><?php echo htmlspecialchars((string)$vs->dateTime); ?></td>
                                                                                                        </tr>
                                                                                                    <?php endforeach; ?>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php endif; ?>

                                                                                <?php if (isset($st->procedures->procedure)): ?>
                                                                                    <div class="mb-2">
                                                                                        <h6 class="fs-14 mb-1">Procedures</h6>
                                                                                        <ul class="list-unstyled mb-0">
                                                                                            <?php foreach ($st->procedures->procedure as $proc): ?>
                                                                                                <li class="mb-1">
                                                                                                    <strong><?php echo htmlspecialchars((string)$proc->procedureName); ?></strong>
                                                                                                    <div class="text-muted">Date: <?php echo htmlspecialchars((string)$proc->dateTime); ?> &middot; Result: <?php echo htmlspecialchars((string)$proc->result); ?></div>
                                                                                                </li>
                                                                                            <?php endforeach; ?>
                                                                                        </ul>
                                                                                    </div>
                                                                                <?php endif; ?>

                                                                                <?php if (isset($st->medications->medication)): ?>
                                                                                    <div class="mb-2">
                                                                                        <h6 class="fs-14 mb-1">Medications</h6>
                                                                                        <ul class="list-unstyled mb-0">
                                                                                            <?php foreach ($st->medications->medication as $med): ?>
                                                                                                <li class="mb-1">
                                                                                                    <strong><?php echo htmlspecialchars((string)$med->medicationName); ?></strong>
                                                                                                    <div class="text-muted">Dosage: <?php echo htmlspecialchars((string)$med->dosage); ?> &middot; Frequency: <?php echo htmlspecialchars((string)$med->frequency); ?></div>
                                                                                                    <div class="text-muted">Start: <?php echo htmlspecialchars((string)$med->startDateTime); ?> &middot; End: <?php echo htmlspecialchars((string)$med->endDateTime); ?></div>
                                                                                                </li>
                                                                                            <?php endforeach; ?>
                                                                                        </ul>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">No patient data found. Ensure <code>web/patient.xml</code> exists.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!--end col-->
                </div>
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