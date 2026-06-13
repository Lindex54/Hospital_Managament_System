<?php

declare(strict_types=1);

require_once __DIR__ . '/data.php';
require_once __DIR__ . '/functions.php';

function hospital_module_page_catalog(): array
{
    return load_app_data('modules.php');
}

function hospital_module_badge_class(string $value): string
{
    $value = strtolower($value);

    if (str_contains($value, 'paid') || str_contains($value, 'completed') || str_contains($value, 'active') || str_contains($value, 'stable') || str_contains($value, 'approved') || str_contains($value, 'available') || str_contains($value, 'ready') || str_contains($value, 'recorded') || str_contains($value, 'live') || str_contains($value, 'pinned')) {
        return 'status-success';
    }

    if (str_contains($value, 'pending') || str_contains($value, 'waiting') || str_contains($value, 'review') || str_contains($value, 'scheduled') || str_contains($value, 'partial') || str_contains($value, 'high')) {
        return 'status-warning';
    }

    if (str_contains($value, 'critical') || str_contains($value, 'low') || str_contains($value, 'urgent') || str_contains($value, 'unpaid') || str_contains($value, 'open')) {
        return 'status-danger';
    }

    return 'badge-info';
}

function render_hospital_module_page(string $key, array $actionAttributes = []): void
{
    $catalog = hospital_module_page_catalog();
    $page = $catalog[$key] ?? null;
    $modalConfig = hospital_module_modal_config($key);

    if ($modalConfig !== null) {
        $actionAttributes[$modalConfig['action_label']] = [
            'data-modal-open' => $modalConfig['modal_id'],
        ];
    }

    if ($page === null) {
        echo '<section class="dashboard-surface"><p class="text-sm text-hospital-secondary">Module page not found.</p></section>';
        return;
    }
    ?>
    <section class="module-page-shell">
        <div class="module-hero">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_auto] xl:items-center xl:gap-6">
                <div class="flex min-w-0 items-start gap-4">
                    <div class="module-hero-icon">
                        <span class="h-7 w-7 [&_svg]:h-7 [&_svg]:w-7"><?= sidebar_icon($page['icon']); ?></span>
                    </div>
                    <div class="min-w-0 xl:max-w-[720px]">
                        <h1 class="page-title"><?= e($page['title']); ?></h1>
                        <p class="mt-2 text-base font-medium text-hospital-secondary"><?= e($page['description']); ?></p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 xl:justify-self-end">
                    <?php foreach ($page['actions'] as $index => $action): ?>
                        <?php
                        $attributes = $actionAttributes[$action] ?? [];
                        $attributeHtml = '';

                        foreach ($attributes as $attributeName => $attributeValue) {
                            $attributeHtml .= ' ' . $attributeName . '="' . e((string) $attributeValue) . '"';
                        }
                        ?>
                        <button class="btn min-w-[170px] justify-center whitespace-nowrap <?= $index === 0 ? 'btn-primary' : 'btn-secondary'; ?>" type="button"<?= $attributeHtml; ?>><?= e($action); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2 2xl:grid-cols-4">
            <?php foreach ($page['stats'] as $stat): ?>
                <article class="dashboard-surface">
                    <p class="text-sm font-bold text-hospital-muted"><?= e($stat[0]); ?></p>
                    <p class="mt-3 font-display text-[2rem] font-extrabold text-hospital-ink"><?= e($stat[1]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="grid gap-6 <?= isset($page['highlights']) ? '2xl:grid-cols-[1.55fr_0.95fr]' : ''; ?>">
            <article class="chart-card">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="section-title"><?= e($page['title']); ?> Records</h3>
                    <a class="text-sm font-bold text-hospital-primary" href="#">View All</a>
                </div>

                <div class="mt-5 table-shell">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <?php foreach ($page['columns'] as $column): ?>
                                    <th><?= e($column); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($page['rows'] as $row): ?>
                                <tr>
                                    <?php foreach ($row as $index => $cell): ?>
                                        <td>
                                            <?php if ($index >= count($row) - 2): ?>
                                                <span class="status-pill <?= e(hospital_module_badge_class((string) $cell)); ?>"><?= e((string) $cell); ?></span>
                                            <?php else: ?>
                                                <?php if ($index === 0 || $index === 1): ?>
                                                    <span class="<?= $index === 1 ? 'font-semibold text-hospital-primary' : 'font-semibold text-hospital-ink'; ?>"><?= e((string) $cell); ?></span>
                                                <?php else: ?>
                                                    <?= e((string) $cell); ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <?php if (isset($page['highlights'])): ?>
                <article class="chart-card">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="section-title">Performance Highlights</h3>
                        <span class="badge badge-info">Static View</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        <?php foreach ($page['highlights'] as $highlight): ?>
                            <div class="insight-card">
                                <p class="insight-label"><?= e($highlight[0]); ?></p>
                                <p class="insight-value"><?= e($highlight[1]); ?></p>
                                <p class="insight-copy"><?= e($highlight[2]); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </section>
    <?php

    if ($modalConfig !== null && is_callable($modalConfig['content'])) {
        render_clinical_modal(
            $modalConfig['modal_id'],
            $modalConfig['title'],
            $modalConfig['subtitle'],
            (string) call_user_func($modalConfig['content'])
        );
    }
}

function hospital_module_modal_config(string $key): ?array
{
    return match ($key) {
        'patients' => [
            'action_label' => 'Register Patient',
            'modal_id' => 'patient-registration',
            'title' => 'Register Patient',
            'subtitle' => 'Patient fields from the `patients` table.',
            'content' => static fn (): string => render_patient_modal_form(),
        ],
        'outpatient' => [
            'action_label' => 'Create OPD Visit',
            'modal_id' => 'outpatient-visit',
            'title' => 'Create OPD Visit',
            'subtitle' => 'Visit fields from the `visits` table.',
            'content' => static fn (): string => render_outpatient_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_departments(),
                clinical_form_fetch_doctors(),
                clinical_form_fetch_appointments()
            ),
        ],
        'inpatient' => [
            'action_label' => 'Admit Patient',
            'modal_id' => 'inpatient-admission',
            'title' => 'Admit Patient',
            'subtitle' => 'Admission fields from the `admissions` table.',
            'content' => static fn (): string => render_inpatient_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_wards(),
                clinical_form_fetch_rooms(),
                clinical_form_fetch_beds(),
                clinical_form_fetch_doctors()
            ),
        ],
        'appointments' => [
            'action_label' => 'Book Appointment',
            'modal_id' => 'appointment-booking',
            'title' => 'Book Appointment',
            'subtitle' => 'Appointment fields from the `appointments` table.',
            'content' => static fn (): string => render_appointment_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_departments(),
                clinical_form_fetch_doctors()
            ),
        ],
        'consultations' => [
            'action_label' => 'New Consultation',
            'modal_id' => 'consultation-notes',
            'title' => 'New Consultation',
            'subtitle' => 'Consultation fields from the `consultations` table.',
            'content' => static fn (): string => render_consultation_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'emergency' => [
            'action_label' => 'Register Emergency',
            'modal_id' => 'emergency-registration',
            'title' => 'Register Emergency',
            'subtitle' => 'Capture emergency intake details for urgent care handling.',
            'content' => static fn (): string => render_emergency_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_departments(),
                clinical_form_fetch_doctors()
            ),
        ],
        'triage' => [
            'action_label' => 'Record Triage',
            'modal_id' => 'triage-record',
            'title' => 'Record Triage',
            'subtitle' => 'Capture queue priority and first clinical observations.',
            'content' => static fn (): string => render_triage_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'emergency-triage' => [
            'action_label' => 'Start Triage',
            'modal_id' => 'emergency-triage-record',
            'title' => 'Start Emergency Triage',
            'subtitle' => 'Document emergency prioritization and immediate handoff actions.',
            'content' => static fn (): string => render_emergency_triage_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'vitals' => [
            'action_label' => 'Record Vitals',
            'modal_id' => 'record-vitals',
            'title' => 'Record Vitals',
            'subtitle' => 'Capture patient observations for the selected visit.',
            'content' => static fn (): string => render_vitals_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_doctors()
            ),
        ],
        'ward-beds' => [
            'action_label' => 'Assign Bed',
            'modal_id' => 'assign-bed',
            'title' => 'Assign Bed',
            'subtitle' => 'Link an admission to the selected ward, room, and bed.',
            'content' => static fn (): string => render_ward_bed_modal_form(
                clinical_form_fetch_admissions(),
                clinical_form_fetch_wards(),
                clinical_form_fetch_rooms(),
                clinical_form_fetch_beds()
            ),
        ],
        'nursing-notes' => [
            'action_label' => 'Add Note',
            'modal_id' => 'nursing-note',
            'title' => 'Add Nursing Note',
            'subtitle' => 'Capture bedside observations and nursing updates.',
            'content' => static fn (): string => render_nursing_notes_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_admissions(),
                clinical_form_fetch_doctors()
            ),
        ],
        'inpatient-admission' => [
            'action_label' => 'Admit Patient',
            'modal_id' => 'inpatient-admission-panel',
            'title' => 'Admit Patient',
            'subtitle' => 'Admission fields from the `admissions` table.',
            'content' => static fn (): string => render_inpatient_modal_form(
                clinical_form_fetch_patients(),
                clinical_form_fetch_visits(),
                clinical_form_fetch_wards(),
                clinical_form_fetch_rooms(),
                clinical_form_fetch_beds(),
                clinical_form_fetch_doctors()
            ),
        ],
        'discharge-referral' => [
            'action_label' => 'Prepare Discharge',
            'modal_id' => 'prepare-discharge',
            'title' => 'Prepare Discharge',
            'subtitle' => 'Discharge fields from the `discharges` table.',
            'content' => static fn (): string => render_discharge_modal_form(
                clinical_form_fetch_admissions(),
                clinical_form_fetch_doctors()
            ),
        ],
        default => null,
    };
}
