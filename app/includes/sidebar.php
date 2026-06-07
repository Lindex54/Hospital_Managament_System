<?php

declare(strict_types=1);

$currentPage = $currentPage ?? '';
$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'href' => base_url('index.php')],
    'patients' => ['label' => 'Patients', 'href' => '#'],
    'outpatient' => ['label' => 'Outpatient', 'href' => '#'],
    'inpatient' => ['label' => 'Inpatient', 'href' => '#'],
    'billing' => ['label' => 'Billing', 'href' => '#'],
    'reports' => ['label' => 'Reports', 'href' => '#'],
];
?>
        <aside class="hidden w-72 bg-brand-900 px-6 py-8 text-white lg:block" data-sidebar>
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-brand-100">System</p>
                <h1 class="mt-2 text-2xl font-bold"><?= e(config('app_name', 'Hospital Management System')); ?></h1>
            </div>

            <nav class="mt-10 space-y-2">
                <?php foreach ($navItems as $key => $item): ?>
                    <a
                        class="block rounded-xl px-4 py-3 text-sm font-medium transition <?= $currentPage === $key ? 'bg-white/15 text-white' : 'text-brand-100 hover:bg-white/10 hover:text-white'; ?>"
                        href="<?= e($item['href']); ?>"
                    >
                        <?= e($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

