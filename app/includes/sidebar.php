<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/data.php';

function sidebar_icon(string $icon): string
{
    /*
    |--------------------------------------------------------------------------
    | Sidebar icon map
    |--------------------------------------------------------------------------
    | Navigation entries only store lightweight icon keys. The SVG markup is
    | centralized here so the navigation data remains clean and permission-
    | focused instead of mixing access rules with presentation details.
    */
    $icons = [
        'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/><rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/></svg>',
        'patients' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>',
        'outpatient' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19h16"/><path d="M5 19V9l3-4h8l3 4v10"/><path d="M9 14h6"/><path d="M12 11v6"/></svg>',
        'inpatient' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12h18"/><path d="M5 12V8h6a2 2 0 0 1 2 2v2"/><path d="M21 16v-5a2 2 0 0 0-2-2h-4"/><path d="M5 12v4"/><path d="M19 12v4"/></svg>',
        'appointments' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M3 10h18"/></svg>',
        'emergency' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3 7h7l-5.5 4.2L18.5 21 12 16.7 5.5 21l2-7.8L2 9h7z"/></svg>',
        'laboratory' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M8 12h8"/></svg>',
        'radiology' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M7 8h.01"/><path d="M17 16h.01"/></svg>',
        'pharmacy' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 7h4"/><path d="M12 5v4"/><rect x="5" y="7" width="14" height="13" rx="3"/><path d="M9 14h6"/></svg>',
        'billing' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h10v18l-3-2-2 2-2-2-3 2z"/><path d="M9 8h6"/><path d="M9 12h6"/></svg>',
        'insurance' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/><path d="M9 12l2 2 4-4"/></svg>',
        'nursing' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-6.5-4.3-9-8.5A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9 6.5C18.5 16.7 12 21 12 21z"/><path d="M12 9v6"/><path d="M9 12h6"/></svg>',
        'consultations' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 10h8"/><path d="M8 14h5"/><path d="M6 4h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-6l-4 4v-4H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/></svg>',
        'triage' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l2.8 5.7L21 9.6l-4.5 4.4 1.1 6.2L12 17.2 6.4 20.2 7.5 14 3 9.6l6.2-.9z"/></svg>',
        'lab-requests' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M14 12h4"/><path d="M16 10v4"/></svg>',
        'sample-collection' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M7 14h10"/></svg>',
        'lab-results' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M8.5 12.5l2 2 4-4"/></svg>',
        'lab-reports' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h10v18l-3-2-2 2-2-2-3 2z"/><path d="M9 8h6"/><path d="M9 12h6"/></svg>',
        'radiology-requests' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>',
        'radiology-results' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M8.5 12.5l2 2 4-4"/></svg>',
        'imaging-reports' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 16l3-3 2 2 4-4"/></svg>',
        'prescriptions' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 7h8"/><path d="M8 12h8"/><path d="M8 17h5"/><rect x="5" y="3" width="14" height="18" rx="2"/></svg>',
        'medicine-dispensing' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5h6"/><path d="M8 3h8l2 2-8 8a3 3 0 0 1-4.2 0L4 11.2A3 3 0 0 1 4 7z"/><path d="M14 14l6 6"/></svg>',
        'pharmacy-stock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h18"/><path d="M5 7v11a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7"/><path d="M9 11h6"/></svg>',
        'pharmacy-reports' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h10v18l-3-2-2 2-2-2-3 2z"/><path d="M9 8h6"/><path d="M9 12h6"/></svg>',
        'vitals' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12h4l2-5 4 10 2-5h6"/></svg>',
        'ward-beds' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12h18"/><path d="M5 12V8h6a2 2 0 0 1 2 2v2"/><path d="M21 16v-5a2 2 0 0 0-2-2h-4"/><path d="M5 12v4"/><path d="M19 12v4"/></svg>',
        'discharge' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 3h7v18h-7"/><path d="M10 7l4 5-4 5"/><path d="M3 12h11"/></svg>',
        'reports' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20v-11"/></svg>',
        'queue' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="7" cy="7" r="3"/><circle cx="17" cy="7" r="3"/><path d="M2 21a5 5 0 0 1 10 0"/><path d="M12 21a5 5 0 0 1 10 0"/></svg>',
        'noticeboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16v10H4z"/><path d="M8 14v6"/><path d="M16 14v6"/></svg>',
        'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.06V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1.4-1.66 1.7 1.7 0 0 0-1.55.4l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.06-.4H2.9a2 2 0 1 1 0-4H3a1.7 1.7 0 0 0 1.66-1.4 1.7 1.7 0 0 0-.4-1.55l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.06V2.9a2 2 0 1 1 4 0V3a1.7 1.7 0 0 0 1.4 1.66 1.7 1.7 0 0 0 1.55-.4l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.26.31.49.65.6 1 .12.33.4.56.75.6h.09a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.35.4z"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8a3 3 0 1 1 0 6"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>',
        'collapse' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 18l-6-6 6-6"/></svg>',
    ];

    return $icons[$icon] ?? $icons['dashboard'];
}

$currentPage = $currentPage ?? '';
$navigationGroups = load_app_data('navigation.php');
?>
        <aside class="sidebar-shell" data-sidebar>
            <div class="sidebar-brand">
                <div class="sidebar-brand-mark">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M10 3.75A2.25 2.25 0 0 1 12.25 1.5h-.5A2.25 2.25 0 0 1 14 3.75V8h4.25a2.25 2.25 0 0 1 2.25 2.25v-.5A2.25 2.25 0 0 1 18.25 12H14v4.25a2.25 2.25 0 0 1-2.25 2.25h.5A2.25 2.25 0 0 1 10 16.25V12H5.75A2.25 2.25 0 0 1 3.5 9.75v.5A2.25 2.25 0 0 1 5.75 8H10z"/>
                    </svg>
                </div>
                <div>
                    <p class="sidebar-brand-title">HMS</p>
                    <p class="sidebar-brand-subtitle">Management System</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($navigationGroups as $group): ?>
                    <?php
                    /*
                     * Every group is filtered against the current user's
                     * permissions before anything is rendered. This keeps the
                     * menu aligned with the RBAC rules defined in the data
                     * layer and avoids hardcoding role checks in the markup.
                     */
                    $visibleItems = array_values(array_filter(
                        $group['items'],
                        static fn (array $item): bool => hasPermission($item['permission'])
                    ));
                    ?>
                    <?php if ($visibleItems === []): ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <div class="mb-5">
                        <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-[0.18em] text-white/45"><?= e($group['label']); ?></p>
                        <div class="space-y-1.5">
                            <?php foreach ($visibleItems as $item): ?>
                                <?php $pageKey = (string) $item['url']; ?>
                                <a
                                    class="sidebar-link flex items-center justify-between gap-3 <?= $currentPage === $pageKey ? 'sidebar-link-active' : ''; ?>"
                                    href="<?= e(base_url('index.php?page=' . $pageKey)); ?>"
                                >
                                    <span class="flex items-center gap-3">
                                        <span class="h-5 w-5 shrink-0 [&_svg]:h-5 [&_svg]:w-5"><?= sidebar_icon($item['icon']); ?></span>
                                        <span><?= e($item['label']); ?></span>
                                    </span>
                                    <span class="sidebar-chevron h-4 w-4 opacity-70 [&_svg]:h-4 [&_svg]:w-4"><?= sidebar_icon('collapse'); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-footer mt-4 border-t border-white/10 pt-4">
                <button class="sidebar-link flex w-full items-center gap-3 rounded-[16px] bg-white/5 text-left" type="button">
                    <span class="h-5 w-5 [&_svg]:h-5 [&_svg]:w-5"><?= sidebar_icon('collapse'); ?></span>
                    <span>Collapse Menu</span>
                </button>
            </div>
        </aside>
