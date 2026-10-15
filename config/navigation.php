<?php

/*
|--------------------------------------------------------------------------
| Navigation tree (with Role-Based Access Control)
|--------------------------------------------------------------------------
| Roles:
|   1 => Superadmin (System Admin)
|   2 => Admin (Branch Admin)
|   3 => Teacher
|   4 => Accountant
|   5 => Librarian
|   6 => Parent
|   7 => Student
*/

return [

    [
        'label' => 'dashboard',
        'icon' => 'home',
        'route' => 'dashboard',
        'roles' => [1, 2, 3, 4, 5, 6, 7],
    ],

    [
        'label' => 'branch',
        'icon' => 'building-office-2',
        'route' => 'branch.index',
        'permission' => ['branch', 'is_view'],
        'roles' => [1],
    ],

    [
        'label' => 'admission',
        'icon' => 'user-plus',
        'children' => [
            ['label' => 'create_admission', 'route' => 'student.create', 'permission' => ['admission', 'is_add']],
            ['label' => 'multiple_import', 'route' => 'student.csv_import', 'permission' => ['admission', 'is_add']],
            ['label' => 'category', 'route' => 'student.category', 'permission' => ['student_category', 'is_view']],
        ],
        'roles' => [1, 2],
    ],

    [
        'label' => 'student_details',
        'icon' => 'academic-cap',
        'children' => [
            ['label' => 'student_list', 'route' => 'student.index', 'permission' => ['student', 'is_view'], 'roles' => [1, 2, 3, 4, 5]],
            ['label' => 'id_card_generate', 'route' => 'student.idcard', 'permission' => ['id_card', 'is_view'], 'roles' => [1, 2, 3]],
            ['label' => 'login_deactivate', 'route' => 'student.disable_authentication', 'permission' => ['disable_authentication', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'promotion', 'route' => 'student.transfer', 'permission' => ['student_promotion', 'is_view'], 'roles' => [1, 2]],
        ],
        'roles' => [1, 2, 3, 4, 5],
    ],

    [
        'label' => 'parents',
        'icon' => 'users',
        'children' => [
            ['label' => 'parents_list', 'route' => 'parents.index', 'permission' => ['parents', 'is_view'], 'roles' => [1, 2, 3]],
            ['label' => 'add_parent', 'route' => 'parents.create', 'permission' => ['parents', 'is_add'], 'roles' => [1, 2]],
            ['label' => 'login_deactivate', 'route' => 'parents.disable_authentication', 'permission' => ['disable_authentication', 'is_view'], 'roles' => [1, 2]],
        ],
        'roles' => [1, 2, 3],
    ],

    [
        'label' => 'employee',
        'icon' => 'identification',
        'children' => [
            ['label' => 'employee_list', 'route' => 'employee.index', 'permission' => ['employee', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'add_department', 'route' => 'employee.department', 'permission' => ['department', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'add_designation', 'route' => 'employee.designation', 'permission' => ['designation', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'add_employee', 'route' => 'employee.create', 'permission' => ['employee', 'is_add'], 'roles' => [1, 2]],
            ['label' => 'login_deactivate', 'route' => 'employee.disable_authentication', 'permission' => ['disable_authentication', 'is_view'], 'roles' => [1, 2]],
        ],
        'roles' => [1, 2],
    ],

    [
        'label' => 'hrm',
        'icon' => 'briefcase',
        'children' => [
            [
                'label' => 'payroll',
                'children' => [
                    ['label' => 'salary_template', 'route' => 'payroll.salary_template', 'permission' => ['salary_template', 'is_view'], 'roles' => [1, 2, 4]],
                    ['label' => 'salary_assign', 'route' => 'payroll.salary_assign', 'permission' => ['salary_assign', 'is_view'], 'roles' => [1, 2, 4]],
                    ['label' => 'salary_payment', 'route' => 'payroll.index', 'permission' => ['salary_payment', 'is_view'], 'roles' => [1, 2, 4]],
                ],
                'roles' => [1, 2, 4],
            ],
            [
                'label' => 'advance_salary',
                'children' => [
                    ['label' => 'my_application', 'route' => 'advancesalary.request', 'permission' => ['advance_salary_request', 'is_view'], 'roles' => [1, 2, 3, 4, 5]],
                    ['label' => 'manage_application', 'route' => 'advancesalary.index', 'permission' => ['advance_salary_manage', 'is_view'], 'roles' => [1, 2, 4]],
                ],
                'roles' => [1, 2, 3, 4, 5],
            ],
            [
                'label' => 'leave',
                'children' => [
                    ['label' => 'category', 'route' => 'leave.category', 'permission' => ['leave_category', 'is_view'], 'roles' => [1, 2]],
                    ['label' => 'my_application', 'route' => 'leave.request', 'permission' => ['leave_request', 'is_view'], 'roles' => [1, 2, 3, 4, 5, 7]],
                    ['label' => 'manage_application', 'route' => 'leave.index', 'permission' => ['leave_manage', 'is_view'], 'roles' => [1, 2, 3]],
                ],
                'roles' => [1, 2, 3, 4, 5, 7],
            ],
            ['label' => 'award', 'route' => 'award.index', 'permission' => ['award', 'is_view'], 'roles' => [1, 2, 3]],
        ],
        'roles' => [1, 2, 3, 4, 5, 7],
    ],

    [
        'label' => 'academic',
        'icon' => 'chalkboard',
        'children' => [
            [
                'label' => 'class_and_section',
                'children' => [
                    ['label' => 'control_classes', 'route' => 'classes.index', 'permission' => ['classes', 'is_view'], 'roles' => [1, 2, 3]],
                    ['label' => 'assign_class_teacher', 'route' => 'classes.teacher_allocation', 'permission' => ['assign_class_teacher', 'is_view'], 'roles' => [1, 2]],
                ],
                'roles' => [1, 2, 3],
            ],
            [
                'label' => 'subject',
                'children' => [
                    ['label' => 'subject', 'route' => 'subject.index', 'permission' => ['subject', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
                    ['label' => 'class_assign', 'route' => 'subject.class_assign', 'permission' => ['subject_class_assign', 'is_view'], 'roles' => [1, 2]],
                    ['label' => 'teacher assign', 'route' => 'subject.teacher_assign', 'permission' => ['subject_teacher_assign', 'is_view'], 'roles' => [1, 2]],
                ],
                'roles' => [1, 2, 3, 6, 7],
            ],
            ['label' => 'class schedule', 'route' => 'timetable.viewclass', 'permission' => ['class_timetable', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
        ],
        'roles' => [1, 2, 3, 6, 7],
    ],

    [
        'label' => 'live_class_rooms',
        'icon' => 'video-camera',
        'route' => 'liveclass.index',
        'permission' => ['live_class', 'is_view'],
        'roles' => [1, 2, 3, 6, 7],
    ],

    [
        'label' => 'attachments_book',
        'icon' => 'cloud-arrow-up',
        'children' => [
            ['label' => 'upload_content', 'route' => 'attachments.index', 'permission' => ['attachments', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'attachment_type', 'route' => 'attachments.type', 'permission' => ['attachment_type', 'is_view'], 'roles' => [1, 2, 3]],
        ],
        'roles' => [1, 2, 3, 6, 7],
    ],

    [
        'label' => 'homework',
        'icon' => 'clipboard-document-list',
        'children' => [
            ['label' => 'homework', 'route' => 'homework.index', 'permission' => ['homework', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'evaluation_report', 'route' => 'homework.report', 'permission' => ['evaluation_report', 'is_view'], 'roles' => [1, 2, 3]],
        ],
        'roles' => [1, 2, 3, 6, 7],
    ],

    [
        'label' => 'exam_master',
        'icon' => 'document-text',
        'children' => [
            ['label' => 'exam_term', 'route' => 'exam.term', 'permission' => ['exam_term', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'exam_hall', 'route' => 'exam.hall', 'permission' => ['exam_hall', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'distribution', 'route' => 'exam.mark_distribution', 'permission' => ['mark_distribution', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'exam_setup', 'route' => 'exam.index', 'permission' => ['exam', 'is_view'], 'roles' => [1, 2, 3]],
            ['label' => 'exam schedule', 'route' => 'exam.schedule', 'permission' => ['exam_timetable', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'exam_marks', 'route' => 'exam.marks', 'permission' => ['exam_mark', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'master_gradesheet', 'route' => 'exam.tabulation', 'permission' => ['exam_mark', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'report_card', 'route' => 'exam.report_card', 'permission' => ['report_card', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'grade', 'route' => 'exam.grade', 'permission' => ['exam_grade', 'is_view'], 'roles' => [1, 2, 3, 7]],
        ],
        'roles' => [1, 2, 3, 6, 7],
    ],

    [
        'label' => 'attendance',
        'icon' => 'calendar-check',
        'children' => [
            ['label' => 'student_attendance', 'route' => 'attendance.student', 'permission' => ['student_attendance', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'staff_attendance', 'route' => 'attendance.staff', 'permission' => ['staff_attendance', 'is_view'], 'roles' => [1, 2, 3, 4, 5]],
        ],
        'roles' => [1, 2, 3, 4, 5, 6, 7],
    ],

    [
        'label' => 'library',
        'icon' => 'book-open',
        'children' => [
            ['label' => 'books', 'route' => 'library.book', 'permission' => ['book', 'is_view'], 'roles' => [1, 2, 3, 5, 7]],
            ['label' => 'book_issue_return', 'route' => 'library.issue', 'permission' => ['book_issue', 'is_view'], 'roles' => [1, 2, 5]],
        ],
        'roles' => [1, 2, 3, 5, 7],
    ],

    [
        'label' => 'transport',
        'icon' => 'truck',
        'children' => [
            ['label' => 'routes', 'route' => 'transport.routes', 'permission' => ['routes', 'is_view'], 'roles' => [1, 2, 3, 6, 7]],
            ['label' => 'vehicles', 'route' => 'transport.vehicles', 'permission' => ['vehicles', 'is_view'], 'roles' => [1, 2, 3]],
            ['label' => 'assign_vehicle', 'route' => 'transport.assign', 'permission' => ['assign_vehicle', 'is_view'], 'roles' => [1, 2]],
        ],
        'roles' => [1, 2, 3, 6, 7],
    ],

    [
        'label' => 'hostel',
        'icon' => 'home-modern',
        'children' => [
            ['label' => 'hostel_list', 'route' => 'hostels.index', 'permission' => ['hostel', 'is_view']],
            ['label' => 'rooms', 'route' => 'hostels.rooms', 'permission' => ['room', 'is_view']],
            ['label' => 'room_assign', 'route' => 'hostels.assign', 'permission' => ['room_assign', 'is_view']],
        ],
        'roles' => [1, 2, 3, 6, 7],
    ],

    [
        'label' => 'fees',
        'icon' => 'receipt-percent',
        'children' => [
            ['label' => 'fees_type', 'route' => 'fees.type', 'permission' => ['fees_type', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'fees_group', 'route' => 'fees.group', 'permission' => ['fees_group', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'fees_master', 'route' => 'fees.master', 'permission' => ['fees_master', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'fee_allocation', 'route' => 'fees.allocation', 'permission' => ['fees_allocation', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'collect_fees', 'route' => 'fees.collect', 'permission' => ['collect_fees', 'is_view'], 'roles' => [1, 2, 4, 6, 7]],
            ['label' => 'fees_reminder', 'route' => 'fees.reminder', 'permission' => ['fees_reminder', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'fine', 'route' => 'fees.fine', 'permission' => ['fine', 'is_view'], 'roles' => [1, 2, 4]],
        ],
        'roles' => [1, 2, 4, 6, 7],
    ],

    [
        'label' => 'accounting',
        'icon' => 'calculator',
        'children' => [
            ['label' => 'transactions', 'route' => 'accounting.index', 'permission' => ['accounting', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'voucher_head', 'route' => 'accounting.voucher_head', 'permission' => ['voucher_head', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'expense_voucher', 'route' => 'accounting.expense_voucher', 'permission' => ['expense_voucher', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'deposit_voucher', 'route' => 'accounting.deposit_voucher', 'permission' => ['deposit_voucher', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'income_report', 'route' => 'accounting.income_report', 'permission' => ['income_report', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'expense_report', 'route' => 'accounting.expense_report', 'permission' => ['expense_report', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'balance_sheet', 'route' => 'accounting.balance_sheet', 'permission' => ['balance_sheet', 'is_view'], 'roles' => [1, 2, 4]],
            ['label' => 'account_statement', 'route' => 'accounting.statement', 'permission' => ['account_statement', 'is_view'], 'roles' => [1, 2, 4]],
        ],
        'roles' => [1, 2, 4],
    ],

    [
        'label' => 'communication',
        'icon' => 'chat-bubble-left-right',
        'children' => [
            ['label' => 'messages', 'route' => 'communication.messages', 'permission' => ['message', 'is_view'], 'roles' => [1, 2, 3, 4, 5, 6, 7]],
            ['label' => 'email_sms_log', 'route' => 'communication.log', 'permission' => ['sms_email_log', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'notice_board', 'route' => 'communication.notice', 'permission' => ['notice', 'is_view'], 'roles' => [1, 2, 3, 4, 5, 6, 7]],
            ['label' => 'event_calendar', 'route' => 'events.index', 'permission' => ['event', 'is_view'], 'roles' => [1, 2, 3, 4, 5, 6, 7]],
        ],
        'roles' => [1, 2, 3, 4, 5, 6, 7],
    ],

    [
        'label' => 'school_settings',
        'icon' => 'cog-6-tooth',
        'children' => [
            ['label' => 'global_settings', 'route' => 'settings.universal', 'permission' => ['global_settings', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'module_control', 'route' => 'settings.modules', 'permission' => ['global_settings', 'is_view'], 'roles' => [1]],
            ['label' => 'general_settings', 'route' => 'settings.school', 'permission' => ['settings', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'session', 'route' => 'sessions.index', 'permission' => ['session', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'roles_permissions', 'route' => 'role.index', 'permission' => ['roles', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'custom_field', 'route' => 'customfield.index', 'permission' => ['custom_field', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'database_backup', 'route' => 'backup.index', 'permission' => ['database_backup', 'is_view'], 'roles' => [1]],
            ['label' => 'language', 'route' => 'translations.index', 'permission' => ['translations', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'payment_gateway', 'route' => 'settings.payment_gateway', 'permission' => ['payment_settings', 'is_view'], 'roles' => [1]],
            ['label' => 'report_card_template', 'route' => 'settings.report_card_template', 'permission' => ['school_settings', 'is_view'], 'roles' => [1, 2]],
            ['label' => 'sms_setting', 'route' => 'settings.smsconfig', 'permission' => ['sms_settings', 'is_view'], 'roles' => [1]],
            ['label' => 'email_setting', 'route' => 'settings.emailconfig', 'permission' => ['email_settings', 'is_view'], 'roles' => [1]],
        ],
        'roles' => [1, 2],
    ],
];
