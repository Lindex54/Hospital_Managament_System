<?php

declare(strict_types=1);
?>
        <div class="content-shell">
            <header class="topbar-shell">
                <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-4">
                        <button class="topbar-icon lg:hidden" data-sidebar-toggle type="button" aria-label="Toggle sidebar">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7h16"/>
                                <path d="M4 12h16"/>
                                <path d="M4 17h16"/>
                            </svg>
                        </button>
                        <div class="search-shell">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-hospital-light">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="M20 20l-3.5-3.5"/>
                                </svg>
                            </span>
                            <input class="search-input" type="text" placeholder="Search patients, visits, invoices..." />
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-hospital-light">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="M20 20l-3.5-3.5"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-4">
                        <button class="topbar-icon relative" type="button" aria-label="Notifications">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                                <path d="M10 21a2 2 0 0 0 4 0"/>
                            </svg>
                            <span class="absolute right-0 top-0 flex h-5 w-5 items-center justify-center rounded-full bg-hospital-danger text-[10px] font-bold text-white">5</span>
                        </button>
                        <button class="topbar-icon" type="button" aria-label="Calendar">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="5" width="18" height="16" rx="2"/>
                                <path d="M16 3v4"/>
                                <path d="M8 3v4"/>
                                <path d="M3 10h18"/>
                            </svg>
                        </button>
                        <div class="flex items-center gap-3 rounded-full border border-hospital-borderSoft bg-white px-2 py-1.5 shadow-sm">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-hospital-primaryLight font-display text-sm font-extrabold text-hospital-primary">AU</div>
                            <div class="hidden pr-1 sm:block">
                                <p class="text-sm font-bold text-hospital-ink">Admin User</p>
                                <p class="text-xs font-medium text-hospital-muted">Administrator</p>
                            </div>
                            <svg class="hidden h-4 w-4 text-hospital-muted sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content-canvas px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
