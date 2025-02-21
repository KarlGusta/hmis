<!-- Sidebar section -->
<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark sidebar-custom">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Logo -->
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href=".">
                <p class="navbar-brand-title">Hospital MIS</p>
            </a>
        </h1>
        <!-- Sidebar items  -->
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">
                <!-- Add Appointments section after Staff Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-appointments" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                <path d="M18 14v4h4" />
                                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M15 3v4" />
                                <path d="M7 3v4" />
                                <path d="M3 11h16" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Appointments</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'appointments'); ?>list.php">All
                            Appointments</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'appointments'); ?>schedule.php">Schedule Appointment</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'appointments'); ?>calendar.php">Appointment Calendar</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'appointments'); ?>reminders.php">Appointment Reminders</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'appointments'); ?>reports.php">Appointment Reports</a>
                    </div>
                </li>

                <!-- Patients Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-patients" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <circle cx="12" cy="7" r="4" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Reception</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'patients'); ?>register.php">Register
                            New Patient</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'patients'); ?>search.php">Patient
                            Search</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'patients'); ?>editlist.php">Patient
                            Edit</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'patients'); ?>list.php">Patient
                            List</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'patients'); ?>appointments.php">Appointments</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'patients'); ?>walk-in.php">Walk-in
                            Patients</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'patients'); ?>check-in.php">Patient
                            Check-in</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'queue'); ?>add.php">Add to Queue</a>
                    </div>
                </li>

                <!-- Queue Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-queue" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 3h14" />
                                <path d="M5 7h14" />
                                <path d="M5 11h14" />
                                <path d="M5 15h14" />
                                <path d="M5 19h14" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Queue</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'queue'); ?>waiting_room.php">Waiting
                            Room</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'queue'); ?>display_board.php">Queue
                            Display</a>
                        <div class="dropend">
                            <a class="dropdown-item dropdown-toggle" href="#sidebar-department-queues"
                                data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                                aria-expanded="false">
                                Department Queues
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/triage.php">Triage</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/outpatient.php">Outpatient</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/laboratory.php">Laboratory</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/pharmacy.php">Pharmacy</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/radiology.php">Radiology</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/dental.php">Dental</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/physiotherapy.php">Physiotherapy</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/mch.php">MCH</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/nutrition.php">Nutrition</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'queue'); ?>departments/theater.php">Theater</a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo path('views', 'queue'); ?>manage.php">Manage Queue</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'queue'); ?>settings.php">Queue
                            Settings</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'queue'); ?>reports.php">Queue
                            Reports</a>
                    </div>
                </li>

                <!-- Triage Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-triage" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M11.99 1.968c1.023 0 1.97 .521 2.512 1.359l.103 .172l7.1 12.25l.062 .126a3 3 0 0 1 -2.568 4.117l-.199 .008h-14.017a3 3 0 0 1 -2.677 -4.157l.09 -.168l7.1 -12.25a3 3 0 0 1 2.494 -1.457z" />
                                <path d="M12 7v4" />
                                <path d="M12 15h.01" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Triage</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'triage'); ?>queue.php">Triage Queue</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'triage'); ?>assessment.php">Patient
                            Assessment</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'triage'); ?>vital_signs.php">Record
                            Vital Signs</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'triage'); ?>history.php">Triage
                            History</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'triage'); ?>reports.php">Triage
                            Reports</a>
                    </div>
                </li>

                <!-- Outpatient -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-outpatient" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                <path d="M12 12l0 .01" />
                                <path d="M3 13a20 20 0 0 0 18 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Outpatient</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'outpatient'); ?>register.php">Register
                            Outpatient</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'outpatient'); ?>consultation.php">Consultation</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'outpatient'); ?>diagnosis.php">Diagnosis & Treatment</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'outpatient'); ?>prescriptions.php">Prescriptions</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'outpatient'); ?>procedures.php">Procedures</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'outpatient'); ?>referrals.php">Referrals</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'outpatient'); ?>follow-up.php">Follow-up Care</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'outpatient'); ?>medical-records.php">Medical Records</a>
                        <div class="dropend">
                            <a class="dropdown-item dropdown-toggle" href="#sidebar-clinics" data-bs-toggle="dropdown"
                                data-bs-auto-close="false" role="button" aria-expanded="false">
                                Specialty Clinics
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/medical.php">Medical
                                    Clinic</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/surgical.php">Surgical
                                    Clinic</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/pediatric.php">Pediatric
                                    Clinic</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/ob-gyn.php">OB-GYN
                                    Clinic</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/ent.php">ENT Clinic</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/eye.php">Eye Clinic</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/orthopedic.php">Orthopedic
                                    Clinic</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'outpatient'); ?>clinics/skin.php">Skin Clinic</a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Add Prescriptions section after Outpatient -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-prescriptions" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                <path d="M10 14h4" />
                                <path d="M12 12v4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Prescriptions</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'prescriptions'); ?>create.php">Create Prescription</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'prescriptions'); ?>pending.php">Pending Prescriptions</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'prescriptions'); ?>history.php">Prescription History</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'prescriptions'); ?>reports.php">Prescription Reports</a>
                    </div>
                </li>

                <!-- Add Consultation section after Outpatient -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-consultation" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21h18" />
                                <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" />
                                <path d="M5 21v-10.15" />
                                <path d="M19 21v-10.15" />
                                <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Consultation</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'consultations'); ?>list.php">Current Consultations</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'consultations'); ?>queue.php">Consultation Queue</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'consultations'); ?>history.php">Patient History</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'consultations'); ?>medications.php">Medications</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'consultations'); ?>reports.php">Consultation Reports</a>
                    </div>
                </li>

                <!-- Billing -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-billing" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Billing</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>paid_bills.php">Paid Bills</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>pending_bills.php">Pending Bills</a>                      
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>reports.php">Billing Reports</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>insurance.php">Insurance Claims</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>pricing.php">Service Pricing</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>packages.php">Billing Packages</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>discounts.php">Discounts</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'billing'); ?>refunds.php">Refunds</a>
                    </div>
                </li>

                <!-- Doctors/Staff -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-staff" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M15 3l6 3l-6 3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Staff Management</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'auth'); ?>register.php">Register
                            User</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'staff'); ?>doctors.php">Doctors</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'staff'); ?>nurses.php">Nurses</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'staff'); ?>employees.php">Employees</a>
                        <div class="dropend">
                            <a class="dropdown-item dropdown-toggle" href="#sidebar-departments"
                                data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                                aria-expanded="false">
                                Departments
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?php echo path('views', 'departments'); ?>list.php">List
                                    Departments</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'departments'); ?>register.php">Add Department</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'departments'); ?>assign.php">Assign Staff</a>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Add Doctors section after Appointments -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-doctors" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 4h-1a2 2 0 0 0 -2 2v3.5h0a5.5 5.5 0 0 0 11 0v-3.5a2 2 0 0 0 -2 -2h-1" />
                                <path d="M8 15h8" />
                                <path d="M12 3v6" />
                                <path d="M3 10h18" />
                                <path d="M8 16l.01 0" />
                                <path d="M16 16l.01 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Doctors</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'doctors'); ?>list.php">All Doctors</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'doctors'); ?>register.php">Register
                            Doctor</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'doctors'); ?>schedules.php">Doctor
                            Schedules</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'doctors'); ?>leaves.php">Leave
                            Management</a>
                        <div class="dropend">
                            <a class="dropdown-item dropdown-toggle" href="#sidebar-specialties"
                                data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                                aria-expanded="false">
                                Specialties
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'doctors'); ?>specialties/list.php">View
                                    Specialties</a>
                                <a class="dropdown-item"
                                    href="<?php echo path('views', 'doctors'); ?>specialties/manage.php">Manage
                                    Specialties</a>
                            </div>
                        </div>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'doctors'); ?>performance.php">Performance Metrics</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'doctors'); ?>ratings.php">Doctor
                            Ratings</a>
                    </div>
                </li>

                <!-- Pharmacy -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-pharmacy" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 3l12 3v14l-12 -3z" />
                                <path d="M6 3v14" />
                                <path d="M18 6l-12 -3" />
                                <path d="M9 11l3 1" />
                                <path d="M9 14l3 1" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Pharmacy</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'medications'); ?>list.php">Medications List</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'medications'); ?>add.php">Add Medication</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'medications'); ?>categories.php">Categories</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'medications'); ?>batches.php">Manage Batches</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'pharmacy'); ?>inventory.php">Inventory</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'pharmacy'); ?>prescriptions.php">Prescriptions</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'pharmacy'); ?>pending_prescriptions.php">Pending Prescriptions</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'pharmacy'); ?>sales.php">Sales</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'pharmacy'); ?>reports.php">Reports</a>
                    </div>
                </li>

                <!-- Laboratory -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-lab" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 8.04l-12.122 12.124a2.857 2.857 0 1 1 -4.041 -4.04l12.122 -12.124" />
                                <path d="M7 15l5 -5" />
                                <path d="M15 7l2 2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Laboratory</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'lab'); ?>tests.php">Test Requests</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'lab'); ?>results.php">Test Results</a>
                    </div>
                </li>

                <!-- Add Nutrition section before MCH Care -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-nutrition" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M16 4h3a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-3v10a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2v-10h-3a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1h3" />
                                <path d="M12 8l-2 4h4l-2 4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Nutrition</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'nutrition'); ?>assessment.php">Nutritional Assessment</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'nutrition'); ?>counseling.php">Nutrition Counseling</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'nutrition'); ?>meal-planning.php">Meal
                            Planning</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'nutrition'); ?>supplements.php">Supplements</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'nutrition'); ?>monitoring.php">Patient
                            Monitoring</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'nutrition'); ?>education.php">Nutrition
                            Education</a>
                    </div>
                </li>

                <!-- MCH Care -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-mch" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M5 14a3 3 0 0 1 3 -3h6a3 3 0 0 1 3 3v3a3 3 0 0 1 -3 3h-6a3 3 0 0 1 -3 -3v-3z" />
                                <path d="M10 9v-4a2 2 0 1 1 4 0v4" />
                                <path d="M9 17l.396 2" />
                                <path d="M15 17l-.396 2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">MCH Care</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'mch'); ?>antenatal.php">Antenatal
                            Care</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'mch') ?>delivery.php">Delivery
                            Services</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'mch'); ?>postnatal.php">Postnatal
                            Care</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'mch'); ?>child-welfare.php">Child
                            Welfare</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'mch'); ?>immunization.php">Immunization</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'mch'); ?>family-planning.php">Family
                            Planning</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'mch'); ?>nutrition.php">Nutrition
                            Services</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'mch'); ?>growth-monitoring.php">Growth
                            Monitoring</a>
                    </div>
                </li>

                <!-- Dental -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-dental" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M12 5.5c-1.074 -.586 -2.583 -1.5 -4 -1.5c-2.1 0 -4 1.247 -4 5c0 4.899 1.056 8.41 2.671 10.537c.573 .756 1.97 .521 2.567 -.236c.398 -.505 .819 -1.439 1.262 -2.801c.292 -.771 .892 -1.504 1.5 -1.5c.602 .004 1.21 .737 1.5 1.5c.443 1.362 .864 2.295 1.262 2.8c.597 .759 1.994 .993 2.567 .237c1.615 -2.127 2.671 -5.637 2.671 -10.537c0 -3.753 -1.9 -5 -4 -5c-1.417 0 -2.926 .914 -4 1.5z" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Dental</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'dental'); ?>appointments.php">Appointments</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'dental'); ?>patient-records.php">Patient Records</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'dental'); ?>treatments.php">Treatments</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'dental'); ?>procedures.php">Procedures</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'dental'); ?>history.php">Treatment
                            History</a>
                    </div>
                </li>

                <!-- Physiotherapy -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-physio" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11 5h2v8h-2z" />
                                <path d="M8 8l8 0" />
                                <path d="M15 13l-2 3l-2 -3" />
                                <path d="M9 21v-8l3 -5l3 5v8" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Physiotherapy</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'physio'); ?>appointments.php">Appointments</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'physio'); ?>assessment.php">Patient
                            Assessment</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'physio'); ?>treatment-plans.php">Treatment Plans</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'physio'); ?>exercises.php">Exercise
                            Programs</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'physio'); ?>progress.php">Progress
                            Notes</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'physio'); ?>equipment.php">Equipment
                            Management</a>
                    </div>
                </li>

                <!-- Add Theater section here -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-theater" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M13 7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8z" />
                                <path d="M13 5v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-4" />
                                <path d="M8 10v.01" />
                                <path d="M8 14v.01" />
                                <path d="M8 18v.01" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Theater</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'theater'); ?>schedule.php">Surgery
                            Schedule</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'theater'); ?>patients.php">Theater
                            Patients</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'theater'); ?>procedures.php">Procedures</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'theater'); ?>equipment.php">Theater
                            Equipment</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'theater'); ?>sterilization.php">Sterilization</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'theater'); ?>recovery.php">Recovery
                            Room</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'theater'); ?>reports.php">Theater
                            Reports</a>
                    </div>
                </li>

                <!-- Inpatient -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-inpatient" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 7v11m0 -4h18m0 4v-8a2 2 0 0 0 -2 -2h-8v6" />
                                <circle cx="7" cy="10" r="1" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Inpatient</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'inpatient'); ?>admissions.php">Admissions</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'inpatient' ); ?>ward-management.php">Ward Management</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'room'); ?>add.php">Add Room</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'room'); ?>list.php">View Rooms</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'room'); ?>bed.php">Bed Management</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'inpatient'); ?>bed-allocation.php">Bed
                            Allocation</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'inpatient'); ?>daily-care.php">Daily
                            Care</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'inpatient'); ?>discharges.php">Discharges</a>
                    </div>
                </li>

                <!-- Hospital Stores -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-stores" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21v-13l9 -4l9 4v13" />
                                <path d="M13 13h4v8h-4" />
                                <path d="M7 13h4v8h-4" />
                                <path d="M12 4v9" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Hospital Stores</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'stores'); ?>inventory.php">Store
                            Inventory</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'stores'); ?>requisitions.php">Requisitions</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'stores'); ?>suppliers.php">Suppliers</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'stores'); ?>purchases.php">Purchase
                            Orders</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'stores'); ?>receiving.php">Goods
                            Receiving</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'stores'); ?>stock-count.php">Stock
                            Count</a>
                    </div>
                </li>

                <!-- Items Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-items" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 4v16h-16v-16" />
                                <path d="M4 4l16 0" />
                                <path d="M8 8l3 0" />
                                <path d="M8 12l3 0" />
                                <path d="M8 16l3 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Items</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'items'); ?>list.php">All Items</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'items'); ?>add_item.php">Add New Item</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'items'); ?>categories.php">Categories</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'items'); ?>stock.php">Stock Levels</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'items'); ?>reports.php">Item Reports</a>
                    </div>
                </li>

                <!-- Insurance -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-insurance" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12v9" />
                                <path d="M12 12l-8 -4.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Insurance</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'insurance'); ?>register.php">Register
                            Provider</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'insurance'); ?>list.php">Insurance
                            Providers</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'insurance'); ?>policies.php">Insurance
                            Policies</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'insurance'); ?>claims.php">Claims
                            Management</a>
                    </div>
                </li>

                <!-- Accounting -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-accounting" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Accounting</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'accounting'); ?>general-ledger.php">General Ledger</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'accounting'); ?>accounts-payable.php">Accounts Payable</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'accounting'); ?>accounts-receivable.php">Accounts
                            Receivable</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'accounting'); ?>cash-management.php">Cash Management</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'accounting'); ?>bank-reconciliation.php">Bank
                            Reconciliation</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'accounting'); ?>financial-reports.php">Financial Reports</a>
                    </div>
                </li>

                <!-- Ministry of Health Reports -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-moh" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 11l3 3l8 -8" />
                                <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" />
                            </svg>
                        </span>
                        <span class="nav-link-title">MOH Reports</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'moh'); ?>workload.php">Workload
                            Reports</a>
                        <a class="dropdown-item"
                            href="<?php echo path('views', 'moh'); ?>disease-surveillance.php">Disease Surveillance</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'moh'); ?>immunization.php">Immunization
                            Reports</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'moh'); ?>maternal.php">Maternal
                            Health</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'moh'); ?>hiv-aids.php">HIV/AIDS
                            Reports</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'moh'); ?>tb.php">TB Reports</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'moh'); ?>malaria.php">Malaria
                            Reports</a>
                    </div>
                </li>

                <!-- Reports -->
                <li class="nav-item">
                    <a class="nav-link" href="../views/reports.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <line x1="9" y1="7" x2="10" y2="7" />
                                <line x1="9" y1="13" x2="15" y2="13" />
                                <line x1="9" y1="17" x2="15" y2="17" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Reports</span>
                    </a>
                </li>

                <!-- User Activities -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-activities" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 12l3 2" />
                                <path d="M12 7v5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">User Activities</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo path('views', 'activities'); ?>list.php">Activity
                            Log</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'activities'); ?>login.php">Login
                            History</a>
                        <a class="dropdown-item" href="<?php echo path('views', 'activities'); ?>system.php">System
                            Events</a>
                    </div>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a class="nav-link" href="../views/settings.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Settings</span>
                    </a>
                </li>

                <!-- Toggle Dropdowns -->
                <li class="nav-item mt-auto">
                    <a class="nav-link" href="#" id="toggleDropdowns">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 6l16 0" />
                                <path d="M4 12l16 0" />
                                <path d="M4 18l16 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Toggle Menus</span>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo path('handlers'); ?>logout.php">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M7 12h14l-3 -3m0 6l3 -3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
<!-- End of sidebar section -->