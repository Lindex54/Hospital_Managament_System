<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/helpers/functions.php';
require_once dirname(__DIR__) . '/app/helpers/data.php';

/*
|--------------------------------------------------------------------------
| Landing page data
|--------------------------------------------------------------------------
| Marketing content is loaded from a dedicated data file so this public page
| stays easy to maintain and consistent with the rest of the project structure.
*/
$landing = load_app_data('landing.php');
$meta = $landing['meta'];
$appName = config('app_name', 'Hospital Management System');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($meta['title']); ?> | <?= e($appName); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset_url('css/output.css')); ?>">
</head>
<body class="landing-body">
    <div class="landing-topbar">
        <div class="landing-container landing-topbar-inner">
            <p><?= e($meta['topbar_text']); ?></p>
            <p class="font-bold text-white"><?= e($meta['support_text']); ?></p>
        </div>
    </div>

    <header class="landing-header">
        <nav class="landing-container landing-nav">
            <a href="<?= e(base_url('landing.php')); ?>" class="flex items-center gap-3">
                <div class="landing-logo-mark">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true">
                        <path d="M10 3.75A2.25 2.25 0 0 1 12.25 1.5h-.5A2.25 2.25 0 0 1 14 3.75V8h4.25a2.25 2.25 0 0 1 2.25 2.25v-.5A2.25 2.25 0 0 1 18.25 12H14v4.25a2.25 2.25 0 0 1-2.25 2.25h.5A2.25 2.25 0 0 1 10 16.25V12H5.75A2.25 2.25 0 0 1 3.5 9.75v.5A2.25 2.25 0 0 1 5.75 8H10z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-display text-xl font-extrabold text-hospital-navy"><?= e($appName); ?></h1>
                    <p class="text-xs font-medium text-hospital-muted">Hospital Management System</p>
                </div>
            </a>

            <div class="landing-nav-links">
                <a href="#features">Features</a>
                <a href="#workflow">Workflow</a>
                <a href="#modules">Modules</a>
                <a href="#preview">Preview</a>
            </div>

            <div class="hidden sm:flex items-center gap-3">
                <a href="<?= e(base_url('index.php')); ?>" class="btn btn-secondary whitespace-nowrap">Open Dashboard</a>
                <a href="#contact" class="btn btn-primary whitespace-nowrap">Contact Us</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="landing-hero">
            <div class="landing-glow landing-glow-right"></div>
            <div class="landing-glow landing-glow-left"></div>

            <div class="landing-container landing-hero-grid">
                <div class="relative z-10">
                    <span class="landing-kicker"><?= e($meta['eyebrow']); ?></span>

                    <h2 class="landing-hero-title"><?= e($meta['headline']); ?></h2>

                    <p class="landing-hero-copy"><?= e($meta['description']); ?></p>

                    <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                        <a href="#modules" class="btn btn-primary min-w-[180px]">Explore Modules</a>
                        <a href="<?= e(base_url('index.php')); ?>" class="btn btn-secondary min-w-[180px]">Open Portal</a>
                    </div>

                    <div class="landing-stat-strip">
                        <?php foreach ($landing['hero_stats'] as $stat): ?>
                            <div>
                                <p class="landing-stat-value"><?= e($stat['value']); ?></p>
                                <p class="landing-stat-label"><?= e($stat['label']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="relative z-10">
                    <?php
                    /*
                     * Use a local generated hero image instead of a remote stock
                     * photo so the landing page stays self-contained while still
                     * keeping the same medical workspace composition.
                     */
                    ?>
                    <img
                        src="<?= e(asset_url('images/landing-hero-medical-workspace.png')); ?>"
                        alt="Hospital workstation with laptop, clinician hands and stethoscope"
                        class="landing-hero-image"
                    >

                    <div class="landing-glass-card">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="landing-mini-label"><?= e($landing['hero_panel']['label']); ?></p>
                                <h3 class="landing-mini-value"><?= e($landing['hero_panel']['value']); ?></h3>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-center">
                                <?php foreach ($landing['hero_panel']['breakdown'] as $panelStat): ?>
                                    <div class="landing-breakdown-card landing-breakdown-<?= e($panelStat['tone']); ?>">
                                        <p class="font-display text-lg font-extrabold"><?= e($panelStat['value']); ?></p>
                                        <p class="text-xs font-semibold text-hospital-muted"><?= e($panelStat['label']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="landing-section bg-white">
            <div class="landing-container">
                <div class="landing-section-intro">
                    <p class="landing-section-kicker">Core Features</p>
                    <h2 class="landing-section-title">Built for real hospital workflows.</h2>
                    <p class="landing-section-copy">Keep one patient record while managing visits, consultations, admissions, investigations, prescriptions and billing from one shared system.</p>
                </div>

                <div class="landing-feature-grid">
                    <?php foreach ($landing['features'] as $feature): ?>
                        <article class="landing-feature-card">
                            <div class="landing-feature-step landing-feature-step-<?= e($feature['tone']); ?>"><?= e($feature['step']); ?></div>
                            <h3 class="mt-5 font-display text-xl font-bold text-hospital-ink"><?= e($feature['title']); ?></h3>
                            <p class="mt-3 text-base leading-7 text-hospital-secondary"><?= e($feature['description']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="workflow" class="landing-section landing-soft-section">
            <div class="landing-container landing-workflow-grid">
                <div class="relative">
                    <?php
                    /*
                     * This section uses a second local generated image focused
                     * on reception-to-discharge workflow instead of an external
                     * stock building photo.
                     */
                    ?>
                    <img
                        src="<?= e(asset_url('images/landing-workflow-patient-journey.png')); ?>"
                        alt="Hospital reception and patient journey scene"
                        class="landing-workflow-image"
                    >
                    <div class="landing-note-card">
                        <p class="font-display text-lg font-extrabold text-hospital-ink">Connected patient journey</p>
                        <p class="mt-2 text-sm leading-6 text-hospital-secondary">From registration to consultation, admission, treatment and discharge, every step stays linked to the same patient story.</p>
                    </div>
                </div>

                <div>
                    <p class="landing-section-kicker">Care Workflow</p>
                    <h2 class="landing-section-title">Smooth movement from reception to discharge.</h2>
                    <p class="landing-section-copy">The system supports outpatient care, inpatient admission, emergency management, diagnostics, prescriptions, pharmacy and financial follow-through without splitting the patient record.</p>

                    <div class="mt-8 space-y-4">
                        <?php foreach ($landing['workflow'] as $step): ?>
                            <article class="landing-workflow-card">
                                <h3 class="font-display text-lg font-bold text-hospital-ink"><?= e($step['title']); ?></h3>
                                <p class="mt-2 text-base leading-7 text-hospital-secondary"><?= e($step['description']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="modules" class="landing-section bg-white">
            <div class="landing-container">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="landing-section-kicker">System Modules</p>
                    <h2 class="landing-section-title">Everything the hospital needs in one system.</h2>
                </div>

                <div class="landing-module-grid">
                    <?php foreach ($landing['modules'] as $module): ?>
                        <article class="landing-module-card">
                            <h3 class="font-display text-xl font-bold text-hospital-ink"><?= e($module['title']); ?></h3>
                            <p class="mt-3 text-base leading-7 text-hospital-secondary"><?= e($module['description']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="preview" class="landing-preview-section">
            <div class="landing-container landing-preview-grid">
                <div>
                    <p class="font-bold text-cyan-200">Dashboard Preview</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold text-white sm:text-4xl">Simple, clean and easy for hospital staff to use.</h2>
                    <p class="mt-5 text-lg leading-8 text-slate-300">Give administrators and care teams a clear view of queue pressure, bed availability, pending results and daily operational performance.</p>

                    <div class="landing-preview-stats">
                        <?php foreach ($landing['dashboard_preview'] as $previewStat): ?>
                            <div class="landing-preview-card">
                                <p class="font-display text-3xl font-extrabold text-white"><?= e($previewStat['value']); ?></p>
                                <p class="mt-2 text-sm font-medium text-slate-300"><?= e($previewStat['label']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="landing-preview-image-shell">
                    <?php
                    /*
                     * The preview panel uses a generated local analytics image
                     * so the dashboard showcase stays visually consistent and
                     * independent from third-party image URLs.
                     */
                    ?>
                    <img
                        src="<?= e(asset_url('images/landing-dashboard-preview.png')); ?>"
                        alt="Hospital dashboard analytics"
                        class="landing-preview-image"
                    >
                </div>
            </div>
        </section>

        <section id="contact" class="landing-section landing-cta-section">
            <div class="landing-container">
                <div class="landing-cta-card">
                    <div class="p-8 sm:p-12">
                        <p class="landing-section-kicker"><?= e($landing['cta']['eyebrow']); ?></p>
                        <h2 class="landing-section-title"><?= e($landing['cta']['headline']); ?></h2>
                        <p class="landing-section-copy"><?= e($landing['cta']['description']); ?></p>

                        <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                            <a href="<?= e(base_url('index.php')); ?>" class="btn btn-primary min-w-[180px]">Open Portal</a>
                            <a href="#features" class="btn btn-secondary min-w-[180px]">View Services</a>
                        </div>
                    </div>

                    <img
                        src="<?= e(asset_url('images/landing-cta-reception.png')); ?>"
                        alt="Modern hospital reception corridor"
                        class="landing-cta-image"
                    >
                </div>
            </div>
        </section>
    </main>

    <footer class="landing-footer">
        <div class="landing-container landing-footer-inner">
            <div>
                <h3 class="font-display text-xl font-extrabold text-white"><?= e($appName); ?></h3>
                <p class="mt-1 text-sm text-slate-300">Integrated hospital operations for better patient care.</p>
            </div>
            <p class="text-sm text-slate-400">&copy; 2026 <?= e($appName); ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
