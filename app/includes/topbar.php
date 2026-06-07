<?php

declare(strict_types=1);
?>
        <div class="flex-1">
            <header class="border-b border-slate-200 bg-white">
                <div class="flex items-center justify-between px-4 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button class="rounded-lg border border-slate-300 px-3 py-2 text-sm lg:hidden" data-sidebar-toggle type="button">
                            Menu
                        </button>
                        <div>
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Overview</p>
                            <h2 class="text-xl font-semibold text-slate-900"><?= e($pageTitle ?? 'Dashboard'); ?></h2>
                        </div>
                    </div>

                    <a class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white" href="<?= e(base_url('login.php')); ?>">
                        Login
                    </a>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6">

