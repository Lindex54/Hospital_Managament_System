<?php

declare(strict_types=1);

function render_clinical_modal(string $id, string $title, string $subtitle, string $content): void
{
    ?>
    <div class="modal-shell" data-modal="<?= e($id); ?>" hidden>
        <div class="modal-backdrop" data-modal-close></div>
        <div class="modal-panel">
            <div class="modal-header">
                <div>
                    <p class="badge badge-info">Quick Form</p>
                    <h2 class="section-title mt-3"><?= e($title); ?></h2>
                    <p class="mt-2 text-sm text-hospital-secondary"><?= e($subtitle); ?></p>
                </div>
                <button class="topbar-icon" type="button" aria-label="Close modal" data-modal-close>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 6l12 12"/>
                        <path d="M18 6l-12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <?= $content; ?>
            </div>
        </div>
    </div>
    <?php
}

function render_patient_modal_form(): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]" method="post">
        <input type="hidden" name="form_action" value="create_patient">
        <article class="panel space-y-6">
            <div>
                <h3 class="section-title">Identity</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div><label for="first_name">First Name<span class="required-mark">*</span></label><input class="form-input mt-2" id="first_name" name="first_name" type="text" required></div>
                    <div><label for="middle_name">Middle Name</label><input class="form-input mt-2" id="middle_name" name="middle_name" type="text"></div>
                    <div><label for="last_name">Last Name<span class="required-mark">*</span></label><input class="form-input mt-2" id="last_name" name="last_name" type="text" required></div>
                    <div><label for="national_id">National ID</label><input class="form-input mt-2" id="national_id" name="national_id" type="text"></div>
                    <div>
                        <label for="date_of_birth">Date of Birth<span class="required-mark">*</span></label>
                        <input class="form-input mt-2" id="date_of_birth" name="date_of_birth" type="date" data-patient-dob required>
                        <p class="helper-text mt-2" data-patient-age>Age will appear here</p>
                    </div>
                    <div><label for="gender">Gender<span class="required-mark">*</span></label><select class="form-input mt-2" id="gender" name="gender" required><option value="">Select gender</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                    <div>
                        <label for="blood_group">Blood Group</label>
                        <select class="form-input mt-2" id="blood_group" name="blood_group">
                            <option value="">Select blood group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div>
                        <label for="marital_status">Marital Status<span class="required-mark">*</span></label>
                        <select class="form-input mt-2" id="marital_status" name="marital_status" required>
                            <option value="">Select marital status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Separated">Separated</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Engaged">Engaged</option>
                            <option value="Cohabiting">Cohabiting</option>
                        </select>
                    </div>
                    <div><label for="status">Status<span class="required-mark">*</span></label><select class="form-input mt-2" id="status" name="status" required><option value="active">Active</option><option value="inactive">Inactive</option><option value="deceased">Deceased</option></select></div>
                </div>
            </div>
            <div>
                <h3 class="section-title">Contacts</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div><label for="phone">Phone<span class="required-mark">*</span></label><input class="form-input mt-2" id="phone" name="phone" type="tel" required></div>
                    <div><label for="alternate_phone">Alternate Phone</label><input class="form-input mt-2" id="alternate_phone" name="alternate_phone" type="tel"></div>
                    <div class="md:col-span-2"><label for="email">Email</label><input class="form-input mt-2" id="email" name="email" type="email"></div>
                    <div class="md:col-span-2"><label for="address_line_1">Address Line 1<span class="required-mark">*</span></label><input class="form-input mt-2" id="address_line_1" name="address_line_1" type="text" required></div>
                    <div class="md:col-span-2"><label for="address_line_2">Address Line 2</label><input class="form-input mt-2" id="address_line_2" name="address_line_2" type="text"></div>
                    <div><label for="city">City<span class="required-mark">*</span></label><input class="form-input mt-2" id="city" name="city" type="text" required></div>
                    <div><label for="district">District<span class="required-mark">*</span></label><input class="form-input mt-2" id="district" name="district" type="text" required></div>
                </div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="emergency_contact_name">Emergency Contact Name<span class="required-mark">*</span></label><input class="form-input mt-2" id="emergency_contact_name" name="emergency_contact_name" type="text" required></div>
            <div><label for="emergency_contact_phone">Emergency Contact Phone<span class="required-mark">*</span></label><input class="form-input mt-2" id="emergency_contact_phone" name="emergency_contact_phone" type="tel" required></div>
            <div><label for="allergies">Allergies</label><textarea class="form-input mt-2 min-h-[130px] py-3" id="allergies" name="allergies"></textarea></div>
            <div><label for="notes">Notes</label><textarea class="form-input mt-2 min-h-[150px] py-3" id="notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Save Patient</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_doctor_modal_form(array $departments): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]" method="post">
        <input type="hidden" name="form_action" value="create_doctor">
        <article class="panel space-y-6">
            <div>
                <h3 class="section-title">Professional Profile</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div><label for="doctor_professional_title">Professional Title<span class="required-mark">*</span></label><select class="form-input mt-2" id="doctor_professional_title" name="professional_title" required><option value="">Select title</option><option value="Dr.">Dr.</option><option value="Prof. Dr.">Prof. Dr.</option><option value="Assoc. Prof. Dr.">Assoc. Prof. Dr.</option><option value="Sr. Dr.">Sr. Dr.</option></select></div>
                    <div><label for="doctor_grade">Doctor Grade<span class="required-mark">*</span></label><select class="form-input mt-2" id="doctor_grade" name="doctor_grade" required><option value="">Select grade</option><option value="Medical Officer">Medical Officer</option><option value="Consultant">Consultant</option><option value="Specialist">Specialist</option><option value="Surgeon">Surgeon</option><option value="Physician">Physician</option><option value="Registrar">Registrar</option><option value="Resident">Resident</option><option value="Intern Doctor">Intern Doctor</option></select></div>
                    <div><label for="doctor_first_name">First Name<span class="required-mark">*</span></label><input class="form-input mt-2" id="doctor_first_name" name="first_name" type="text" required></div>
                    <div><label for="doctor_last_name">Last Name<span class="required-mark">*</span></label><input class="form-input mt-2" id="doctor_last_name" name="last_name" type="text" required></div>
                    <div><label for="doctor_gender">Gender<span class="required-mark">*</span></label><select class="form-input mt-2" id="doctor_gender" name="gender" required><option value="">Select gender</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                    <div><label for="doctor_department_id">Department<span class="required-mark">*</span></label><select class="form-input mt-2" id="doctor_department_id" name="department_id" required><option value="">Select department</option><?php foreach ($departments as $department): ?><option value="<?= e((string) $department['id']); ?>"><?= e((string) $department['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="md:col-span-2"><label for="doctor_specialty_focus">Specialty / Focus Area</label><input class="form-input mt-2" id="doctor_specialty_focus" name="specialty_focus" type="text" placeholder="e.g. Cardiology, Pediatrics, General Surgery"></div>
                    <div><label for="doctor_hire_date">Hire Date<span class="required-mark">*</span></label><input class="form-input mt-2" id="doctor_hire_date" name="hire_date" type="date" required></div>
                    <div><label for="doctor_status">Status<span class="required-mark">*</span></label><select class="form-input mt-2" id="doctor_status" name="status" required><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="doctor_phone">Phone<span class="required-mark">*</span></label><input class="form-input mt-2" id="doctor_phone" name="phone" type="tel" required></div>
            <div><label for="doctor_email">Email</label><input class="form-input mt-2" id="doctor_email" name="email" type="email"></div>
            <div class="rounded-xl border border-hospital-borderSoft bg-white px-4 py-4">
                <p class="text-sm font-bold text-hospital-ink">How this works</p>
                <p class="mt-2 text-sm leading-6 text-hospital-secondary">Every doctor you save here is written to the `staff` table and becomes available immediately in doctor assignment dropdowns across visits, appointments, consultations, emergency intake, and admission workflows.</p>
            </div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Save Doctor</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_outpatient_modal_form(array $patients, array $departments, array $doctors, array $appointments): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]" method="post">
        <input type="hidden" name="form_action" value="create_outpatient_visit">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div><label for="opd_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="opd_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="opd_appointment_id">Appointment</label><select class="form-input mt-2" id="opd_appointment_id" name="appointment_id"><option value="">No linked appointment</option><?php foreach ($appointments as $appointment): ?><option value="<?= e((string) $appointment['id']); ?>"><?= e((string) $appointment['patient_number'] . ' - ' . clinical_form_patient_name($appointment) . ' - ' . $appointment['appointment_date']); ?></option><?php endforeach; ?></select></div>
                <div><label for="opd_department_id">Department<span class="required-mark">*</span></label><select class="form-input mt-2" id="opd_department_id" name="department_id" required><option value="">Select department</option><?php foreach ($departments as $department): ?><option value="<?= e((string) $department['id']); ?>"><?= e((string) $department['name']); ?></option><?php endforeach; ?></select></div>
                <div><label for="opd_doctor_id">Doctor</label><select class="form-input mt-2" id="opd_doctor_id" name="doctor_id"><option value="">Assign doctor</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['department_name'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="visit_date">Visit Date & Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="visit_date" name="visit_date" type="datetime-local" required></div>
                <div><label for="visit_type">Visit Type<span class="required-mark">*</span></label><select class="form-input mt-2" id="visit_type" name="visit_type" required><option value="outpatient" selected>Outpatient</option><option value="follow_up">Follow Up</option><option value="emergency">Emergency</option><option value="inpatient">Inpatient</option></select></div>
                <div><label for="visit_status">Visit Status<span class="required-mark">*</span></label><select class="form-input mt-2" id="visit_status" name="visit_status" required><option value="registered">Registered</option><option value="triage">Triage</option><option value="consulting">Consulting</option><option value="admitted">Admitted</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="chief_complaint">Chief Complaint<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[140px] py-3" id="chief_complaint" name="chief_complaint" required></textarea></div>
            <div><label for="visit_notes">Notes</label><textarea class="form-input mt-2 min-h-[170px] py-3" id="visit_notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Create Visit</button><button class="btn btn-secondary" type="reset">Clear Fields</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_inpatient_modal_form(array $patients, array $visits, array $wards, array $rooms, array $beds, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]" method="post">
        <input type="hidden" name="form_action" value="create_inpatient_admission">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div><label for="admission_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="admission_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="admission_visit_id">Linked Visit<span class="required-mark">*</span></label><select class="form-input mt-2" id="admission_visit_id" name="visit_id" required><option value="">Select visit</option><?php foreach ($visits as $visit): ?><option value="<?= e((string) $visit['id']); ?>"><?= e((string) $visit['visit_number'] . ' - ' . clinical_form_patient_name($visit)); ?></option><?php endforeach; ?></select></div>
                <div><label for="admitted_by">Admitted By<span class="required-mark">*</span></label><select class="form-input mt-2" id="admitted_by" name="admitted_by" required><option value="">Select staff</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['job_title'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="ward_id">Ward<span class="required-mark">*</span></label><select class="form-input mt-2" id="ward_id" name="ward_id" data-ward-filter required><option value="">Select ward</option><?php foreach ($wards as $ward): ?><option value="<?= e((string) $ward['id']); ?>"><?= e((string) $ward['name']); ?></option><?php endforeach; ?></select></div>
                <div><label for="room_id">Room<span class="required-mark">*</span></label><select class="form-input mt-2" id="room_id" name="room_id" data-room-filter required><option value="">Select room</option><?php foreach ($rooms as $room): ?><option value="<?= e((string) $room['id']); ?>" data-ward-id="<?= e((string) $room['ward_id']); ?>"><?= e((string) $room['room_number'] . ' - ' . $room['name']); ?></option><?php endforeach; ?></select></div>
                <div><label for="bed_id">Bed<span class="required-mark">*</span></label><select class="form-input mt-2" id="bed_id" name="bed_id" data-bed-filter required><option value="">Select bed</option><?php foreach ($beds as $bed): ?><option value="<?= e((string) $bed['id']); ?>" data-room-id="<?= e((string) $bed['room_id']); ?>"><?= e((string) $bed['bed_number'] . ' - ' . ucfirst((string) $bed['status'])); ?></option><?php endforeach; ?></select></div>
                <div><label for="admission_date">Admission Date & Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="admission_date" name="admission_date" type="datetime-local" required></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="admission_status">Status<span class="required-mark">*</span></label><select class="form-input mt-2" id="admission_status" name="status" required><option value="active">Active</option><option value="discharged">Discharged</option><option value="cancelled">Cancelled</option></select></div>
            <div><label for="admission_reason">Reason<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[130px] py-3" id="admission_reason" name="reason" required></textarea></div>
            <div><label for="admission_notes">Notes</label><textarea class="form-input mt-2 min-h-[160px] py-3" id="admission_notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Admit Patient</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_appointment_modal_form(array $patients, array $departments, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1fr_1fr]" method="post">
        <input type="hidden" name="form_action" value="create_appointment">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2"><label for="appointment_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="appointment_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="appointment_department_id">Department<span class="required-mark">*</span></label><select class="form-input mt-2" id="appointment_department_id" name="department_id" required><option value="">Select department</option><?php foreach ($departments as $department): ?><option value="<?= e((string) $department['id']); ?>"><?= e((string) $department['name']); ?></option><?php endforeach; ?></select></div>
                <div><label for="appointment_doctor_id">Doctor</label><select class="form-input mt-2" id="appointment_doctor_id" name="doctor_id"><option value="">Select doctor</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['department_name'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="appointment_date">Appointment Date & Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="appointment_date" name="appointment_date" type="datetime-local" required></div>
                <div><label for="appointment_status">Status<span class="required-mark">*</span></label><select class="form-input mt-2" id="appointment_status" name="status" required><option value="scheduled">Scheduled</option><option value="confirmed">Confirmed</option><option value="cancelled">Cancelled</option><option value="completed">Completed</option><option value="no_show">No Show</option></select></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="appointment_reason">Reason<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[140px] py-3" id="appointment_reason" name="reason" required></textarea></div>
            <div><label for="appointment_notes">Notes</label><textarea class="form-input mt-2 min-h-[170px] py-3" id="appointment_notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Book Appointment</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_consultation_modal_form(array $patients, array $visits, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]" method="post">
        <input type="hidden" name="form_action" value="create_consultation">
        <article class="panel space-y-6">
            <div class="grid gap-4">
                <div><label for="consultation_visit_id">Visit<span class="required-mark">*</span></label><select class="form-input mt-2" id="consultation_visit_id" name="visit_id" required><option value="">Select visit</option><?php foreach ($visits as $visit): ?><option value="<?= e((string) $visit['id']); ?>"><?= e((string) $visit['visit_number'] . ' - ' . clinical_form_patient_name($visit) . ' - ' . ($visit['department_name'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="consultation_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="consultation_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="consultation_doctor_id">Doctor<span class="required-mark">*</span></label><select class="form-input mt-2" id="consultation_doctor_id" name="doctor_id" required><option value="">Select doctor</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['department_name'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="consultation_date">Consultation Date & Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="consultation_date" name="consultation_date" type="datetime-local" required></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="symptoms">Symptoms<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[110px] py-3" id="symptoms" name="symptoms" required></textarea></div>
            <div><label for="diagnosis">Diagnosis<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[110px] py-3" id="diagnosis" name="diagnosis" required></textarea></div>
            <div><label for="treatment_plan">Treatment Plan<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[120px] py-3" id="treatment_plan" name="treatment_plan" required></textarea></div>
            <div><label for="clinical_notes">Clinical Notes</label><textarea class="form-input mt-2 min-h-[160px] py-3" id="clinical_notes" name="clinical_notes"></textarea></div>
            <div><label for="follow_up_instructions">Follow Up Instructions</label><textarea class="form-input mt-2 min-h-[120px] py-3" id="follow_up_instructions" name="follow_up_instructions"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Save Consultation</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_emergency_modal_form(array $patients, array $departments, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1fr_1fr]" method="post">
        <input type="hidden" name="form_action" value="create_emergency_visit">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2"><label for="emergency_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="emergency_department_id">Department<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_department_id" name="department_id" required><option value="">Select department</option><?php foreach ($departments as $department): ?><option value="<?= e((string) $department['id']); ?>"><?= e((string) $department['name']); ?></option><?php endforeach; ?></select></div>
                <div><label for="emergency_doctor_id">Assigned Doctor</label><select class="form-input mt-2" id="emergency_doctor_id" name="doctor_id"><option value="">Assign doctor</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['department_name'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="emergency_arrival_mode">Arrival Mode<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_arrival_mode" name="arrival_mode" required><option value="">Select arrival mode</option><option value="ambulance">Ambulance</option><option value="walk_in">Walk In</option><option value="referral">Referral</option><option value="police">Police</option><option value="private_vehicle">Private Vehicle</option></select></div>
                <div><label for="emergency_priority">Priority Level<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_priority" name="priority_level" required><option value="">Select priority</option><option value="critical">Critical</option><option value="urgent">Urgent</option><option value="semi_urgent">Semi Urgent</option><option value="stable">Stable</option></select></div>
                <div><label for="emergency_arrival_time">Arrival Date & Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="emergency_arrival_time" name="arrival_time" type="datetime-local" required></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="emergency_complaint">Presenting Complaint<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[130px] py-3" id="emergency_complaint" name="presenting_complaint" required></textarea></div>
            <div><label for="emergency_status">Status<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_status" name="status" required><option value="open">Open</option><option value="stabilized">Stabilized</option><option value="admitted">Admitted</option><option value="closed">Closed</option></select></div>
            <div><label for="emergency_notes">Notes</label><textarea class="form-input mt-2 min-h-[170px] py-3" id="emergency_notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Save Emergency Case</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_triage_modal_form(array $patients, array $visits, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1fr_1fr]" method="post">
        <input type="hidden" name="form_action" value="create_triage_record">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2"><label for="triage_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="triage_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="triage_visit_id">Linked Visit</label><select class="form-input mt-2" id="triage_visit_id" name="visit_id"><option value="">Select visit</option><?php foreach ($visits as $visit): ?><option value="<?= e((string) $visit['id']); ?>"><?= e((string) $visit['visit_number'] . ' - ' . clinical_form_patient_name($visit)); ?></option><?php endforeach; ?></select></div>
                <div><label for="triage_nurse_id">Triage Nurse<span class="required-mark">*</span></label><select class="form-input mt-2" id="triage_nurse_id" name="nurse_id" required><option value="">Select staff</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['job_title'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="triage_level">Triage Level<span class="required-mark">*</span></label><select class="form-input mt-2" id="triage_level" name="triage_level" required><option value="">Select level</option><option value="red">Red</option><option value="orange">Orange</option><option value="yellow">Yellow</option><option value="green">Green</option><option value="blue">Blue</option></select></div>
                <div><label for="triage_status">Queue Status<span class="required-mark">*</span></label><select class="form-input mt-2" id="triage_status" name="status" required><option value="queued">Queued</option><option value="serving">Serving</option><option value="completed">Completed</option><option value="referred">Referred</option></select></div>
                <div><label for="triage_time">Triage Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="triage_time" name="triage_time" type="datetime-local" required></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="triage_complaint">Complaint Summary<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[120px] py-3" id="triage_complaint" name="complaint_summary" required></textarea></div>
            <div><label for="triage_observations">Initial Observations<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[140px] py-3" id="triage_observations" name="observations" required></textarea></div>
            <div><label for="triage_notes">Notes</label><textarea class="form-input mt-2 min-h-[160px] py-3" id="triage_notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Save Triage</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_emergency_triage_modal_form(array $patients, array $visits, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1fr_1fr]" method="post">
        <input type="hidden" name="form_action" value="create_emergency_triage_record">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2"><label for="emergency_triage_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_triage_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="emergency_triage_visit_id">Emergency Visit</label><select class="form-input mt-2" id="emergency_triage_visit_id" name="visit_id"><option value="">Select visit</option><?php foreach ($visits as $visit): ?><option value="<?= e((string) $visit['id']); ?>"><?= e((string) $visit['visit_number'] . ' - ' . clinical_form_patient_name($visit) . ' - ' . ucfirst((string) ($visit['visit_type'] ?? 'visit'))); ?></option><?php endforeach; ?></select></div>
                <div><label for="emergency_triage_nurse_id">Triage Officer<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_triage_nurse_id" name="nurse_id" required><option value="">Select staff</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['job_title'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="emergency_triage_priority">Emergency Priority<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_triage_priority" name="priority" required><option value="">Select priority</option><option value="critical">Critical</option><option value="urgent">Urgent</option><option value="moderate">Moderate</option><option value="minor">Minor</option></select></div>
                <div><label for="emergency_triage_outcome">Immediate Outcome<span class="required-mark">*</span></label><select class="form-input mt-2" id="emergency_triage_outcome" name="outcome" required><option value="">Select outcome</option><option value="doctor_review">Doctor Review</option><option value="observation">Observation</option><option value="lab_request">Lab Request</option><option value="admission">Admission</option><option value="transfer">Transfer</option></select></div>
                <div><label for="emergency_triage_time">Triage Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="emergency_triage_time" name="triage_time" type="datetime-local" required></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="emergency_triage_findings">Emergency Findings<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[130px] py-3" id="emergency_triage_findings" name="findings" required></textarea></div>
            <div><label for="emergency_triage_action">Immediate Action Taken<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[140px] py-3" id="emergency_triage_action" name="action_taken" required></textarea></div>
            <div><label for="emergency_triage_notes">Handover Notes</label><textarea class="form-input mt-2 min-h-[160px] py-3" id="emergency_triage_notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Start Triage</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_vitals_modal_form(array $patients, array $visits, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]" method="post">
        <input type="hidden" name="form_action" value="record_vitals">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div><label for="vitals_visit_id">Visit<span class="required-mark">*</span></label><select class="form-input mt-2" id="vitals_visit_id" name="visit_id" required><option value="">Select visit</option><?php foreach ($visits as $visit): ?><option value="<?= e((string) $visit['id']); ?>"><?= e((string) $visit['visit_number'] . ' - ' . clinical_form_patient_name($visit)); ?></option><?php endforeach; ?></select></div>
                <div><label for="vitals_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="vitals_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="vitals_recorded_by">Recorded By<span class="required-mark">*</span></label><select class="form-input mt-2" id="vitals_recorded_by" name="recorded_by" required><option value="">Select staff</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['job_title'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="vitals_recorded_at">Recorded At<span class="required-mark">*</span></label><input class="form-input mt-2" id="vitals_recorded_at" name="recorded_at" type="datetime-local" required></div>
                <div><label for="vitals_systolic">Systolic BP</label><input class="form-input mt-2" id="vitals_systolic" name="systolic_bp" type="number" min="0"></div>
                <div><label for="vitals_diastolic">Diastolic BP</label><input class="form-input mt-2" id="vitals_diastolic" name="diastolic_bp" type="number" min="0"></div>
                <div><label for="vitals_temperature">Temperature (C)</label><input class="form-input mt-2" id="vitals_temperature" name="temperature" type="number" min="0" step="0.1"></div>
                <div><label for="vitals_pulse">Pulse Rate</label><input class="form-input mt-2" id="vitals_pulse" name="pulse_rate" type="number" min="0"></div>
                <div><label for="vitals_respiratory">Respiratory Rate</label><input class="form-input mt-2" id="vitals_respiratory" name="respiratory_rate" type="number" min="0"></div>
                <div><label for="vitals_oxygen">Oxygen Saturation (%)</label><input class="form-input mt-2" id="vitals_oxygen" name="oxygen_saturation" type="number" min="0" max="100" step="0.1"></div>
                <div><label for="vitals_weight">Weight (kg)</label><input class="form-input mt-2" id="vitals_weight" name="weight_kg" type="number" min="0" step="0.1"></div>
                <div><label for="vitals_height">Height (cm)</label><input class="form-input mt-2" id="vitals_height" name="height_cm" type="number" min="0" step="0.1"></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="vitals_notes">Notes</label><textarea class="form-input mt-2 min-h-[220px] py-3" id="vitals_notes" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Record Vitals</button><button class="btn btn-secondary" type="reset">Clear Fields</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_ward_bed_modal_form(array $admissions, array $wards, array $rooms, array $beds): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1fr_1fr]" method="post">
        <input type="hidden" name="form_action" value="assign_bed">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2"><label for="bed_assignment_admission_id">Admission<span class="required-mark">*</span></label><select class="form-input mt-2" id="bed_assignment_admission_id" name="admission_id" required><option value="">Select admission</option><?php foreach ($admissions as $admission): ?><option value="<?= e((string) $admission['id']); ?>"><?= e((string) $admission['admission_number'] . ' - ' . clinical_form_patient_name($admission)); ?></option><?php endforeach; ?></select></div>
                <div><label for="bed_assignment_ward_id">Ward<span class="required-mark">*</span></label><select class="form-input mt-2" id="bed_assignment_ward_id" name="ward_id" data-ward-filter required><option value="">Select ward</option><?php foreach ($wards as $ward): ?><option value="<?= e((string) $ward['id']); ?>"><?= e((string) $ward['name']); ?></option><?php endforeach; ?></select></div>
                <div><label for="bed_assignment_room_id">Room<span class="required-mark">*</span></label><select class="form-input mt-2" id="bed_assignment_room_id" name="room_id" data-room-filter required><option value="">Select room</option><?php foreach ($rooms as $room): ?><option value="<?= e((string) $room['id']); ?>" data-ward-id="<?= e((string) $room['ward_id']); ?>"><?= e((string) $room['room_number'] . ' - ' . $room['name']); ?></option><?php endforeach; ?></select></div>
                <div><label for="bed_assignment_bed_id">Bed<span class="required-mark">*</span></label><select class="form-input mt-2" id="bed_assignment_bed_id" name="bed_id" data-bed-filter required><option value="">Select bed</option><?php foreach ($beds as $bed): ?><option value="<?= e((string) $bed['id']); ?>" data-room-id="<?= e((string) $bed['room_id']); ?>"><?= e((string) $bed['bed_number'] . ' - ' . ucfirst((string) $bed['status'])); ?></option><?php endforeach; ?></select></div>
                <div><label for="bed_assignment_date">Assignment Date & Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="bed_assignment_date" name="assigned_at" type="datetime-local" required></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="bed_assignment_reason">Reason / Notes</label><textarea class="form-input mt-2 min-h-[210px] py-3" id="bed_assignment_reason" name="notes"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Assign Bed</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_nursing_notes_modal_form(array $patients, array $admissions, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1fr_1fr]" method="post">
        <input type="hidden" name="form_action" value="create_nursing_note">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div><label for="nursing_patient_id">Patient<span class="required-mark">*</span></label><select class="form-input mt-2" id="nursing_patient_id" name="patient_id" required><option value="">Select patient</option><?php foreach ($patients as $patient): ?><option value="<?= e((string) $patient['id']); ?>"><?= e((string) $patient['patient_number'] . ' - ' . clinical_form_patient_name($patient)); ?></option><?php endforeach; ?></select></div>
                <div><label for="nursing_admission_id">Admission</label><select class="form-input mt-2" id="nursing_admission_id" name="admission_id"><option value="">Select admission</option><?php foreach ($admissions as $admission): ?><option value="<?= e((string) $admission['id']); ?>"><?= e((string) $admission['admission_number'] . ' - ' . clinical_form_patient_name($admission)); ?></option><?php endforeach; ?></select></div>
                <div><label for="nursing_staff_id">Nurse / Staff<span class="required-mark">*</span></label><select class="form-input mt-2" id="nursing_staff_id" name="staff_id" required><option value="">Select staff</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['job_title'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="nursing_note_type">Note Type<span class="required-mark">*</span></label><select class="form-input mt-2" id="nursing_note_type" name="note_type" required><option value="">Select type</option><option value="observation">Observation</option><option value="medication">Medication</option><option value="care_plan">Care Plan</option><option value="handover">Handover</option></select></div>
                <div class="md:col-span-2"><label for="nursing_recorded_at">Recorded At<span class="required-mark">*</span></label><input class="form-input mt-2" id="nursing_recorded_at" name="recorded_at" type="datetime-local" required></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="nursing_note_body">Nursing Note<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[190px] py-3" id="nursing_note_body" name="note_body" required></textarea></div>
            <div><label for="nursing_plan">Plan / Follow Up</label><textarea class="form-input mt-2 min-h-[140px] py-3" id="nursing_plan" name="plan"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Save Note</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}

function render_discharge_modal_form(array $admissions, array $doctors): string
{
    ob_start();
    ?>
    <form class="grid gap-6 xl:grid-cols-[1fr_1fr]" method="post">
        <input type="hidden" name="form_action" value="create_discharge_record">
        <article class="panel space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2"><label for="discharge_admission_id">Admission<span class="required-mark">*</span></label><select class="form-input mt-2" id="discharge_admission_id" name="admission_id" required><option value="">Select admission</option><?php foreach ($admissions as $admission): ?><option value="<?= e((string) $admission['id']); ?>"><?= e((string) $admission['admission_number'] . ' - ' . clinical_form_patient_name($admission) . ' - ' . ($admission['ward_name'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="discharged_by">Discharged By<span class="required-mark">*</span></label><select class="form-input mt-2" id="discharged_by" name="discharged_by" required><option value="">Select staff</option><?php foreach ($doctors as $doctor): ?><option value="<?= e((string) $doctor['id']); ?>"><?= e((string) clinical_form_doctor_name($doctor) . ' - ' . ($doctor['job_title'] ?? '')); ?></option><?php endforeach; ?></select></div>
                <div><label for="discharge_date">Discharge Date & Time<span class="required-mark">*</span></label><input class="form-input mt-2" id="discharge_date" name="discharge_date" type="datetime-local" required></div>
                <div><label for="discharge_condition">Discharge Condition<span class="required-mark">*</span></label><input class="form-input mt-2" id="discharge_condition" name="discharge_condition" type="text" required></div>
                <div><label for="discharge_outcome">Outcome<span class="required-mark">*</span></label><select class="form-input mt-2" id="discharge_outcome" name="outcome" required><option value="">Select outcome</option><option value="discharged_home">Discharged Home</option><option value="referred">Referred</option><option value="transferred">Transferred</option><option value="deceased">Deceased</option></select></div>
            </div>
        </article>
        <article class="panel space-y-4">
            <div><label for="discharge_summary">Discharge Summary<span class="required-mark">*</span></label><textarea class="form-input mt-2 min-h-[150px] py-3" id="discharge_summary" name="discharge_summary" required></textarea></div>
            <div><label for="discharge_instructions">Instructions</label><textarea class="form-input mt-2 min-h-[120px] py-3" id="discharge_instructions" name="instructions"></textarea></div>
            <div class="flex flex-wrap gap-3 pt-2"><button class="btn btn-primary" type="submit">Prepare Discharge</button><button class="btn btn-secondary" type="reset">Reset Form</button></div>
        </article>
    </form>
    <?php

    return (string) ob_get_clean();
}
