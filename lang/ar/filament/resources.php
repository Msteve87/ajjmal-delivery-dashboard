<?php

return [
    'driver' => [
        'label' => 'السائق',
        'plural_label' => 'السائقين',
        'schema' => [
            'id' => 'الرقم',
            'license_no' => 'رقم الرخصة',
            'name' => 'إسم السائق',
            'first_name' => 'الاسم الأول',
            'last_name' => 'الاسم الأخير',
            'phone' => 'رقم الهاتف',
            'password' => 'كلمة المرور',
            'driver_type' => 'نوع السائق',
            'gender' => 'الجنس',
            'criminal_case' => 'الحالة الجنائية',
            'delivery_status' => 'حالة التوصيل',
            'passport_number' => 'رقم جواز السفر',
            'national_number' => 'الرقم الوطني',
            'date_of_birth' => 'تاريخ الميلاد',
            'status' => 'الحالة',
            'is_active' => 'مفعل',
        ],
        'form' => [
            'first_name' => 'الاسم الأول',
            'last_name' => 'الاسم الأخير',
            'phone' => 'رقم الهاتف',
            'password' => 'كلمة المرور',
            'attachments' => 'المرفقات',
            'vehicle_registration' => 'تسجيل المركبة',
            'vehicle_insurance' => 'تأمين المركبة',
            'vehicle_license' => 'صورة من رخصة التجوال',
            'license_no' => 'رقم الرخصة',
            'passport_no' => 'رقم جواز السفر',
            'criminal_case' => 'الحالة الجنائية',
            'national_no' => 'الرقم الوطني',
            'vehicle_insurance_attachment' => 'صورة من تأمين المركبة',
            'vehicle_registration_attachment' => 'صورة من تسجيل المركبة',
            'vehicle_license_attachment' => 'صورة من رخصة التجوال',
            'license_attachment' => 'صورة من الرخصة',
            'passport_attachment' => 'صورة من جواز السفر',
            'criminal_case_attachment' => 'صورة من الحالة الجنائية',
            'documents' => 'مستندات السائق'
        ],
        'view-page' => [
            'label' => 'عرض السائق',
            'tables' => [
                'delivered_sub_orders' => 'الطلبات التي تم توصيلها',
            ]
        ]
    ],
    'order' => [
        'label' => 'الطلب الرئيسي',
        'plural_label' => 'الطلبات الرئيسية',
        'schema' => [
            'id' => 'المعرف',
            'jm_order_id' => 'معرف الطلب في النظام',
            'reference' => 'الرقم المرجعي',
            'payment_method' => 'طريقة الدفع',
            'customer_name' => 'اسم العميل',
            'driver_name' => 'اسم السائق',
            'status' => 'الحالة',
            'price' => 'السعر',
            'total_shipping' => 'إجمالي الشحن',
            'total_paid' => 'إجمالي المدفوع',
            'delivery_date' => 'تاريخ التسليم',
            'start_time' => 'وقت البدء',
            'end_time' => 'وقت الانتهاء',
            'address' => 'العنوان',
            'customer_phone' => 'هاتف العميل',
            'products' => 'المنتجات',
        ],
        'actions' => [
            'assign_driver' => 'تعيين سائق',
        ],
    ],
    'sub_order' => [
        'label' => 'الطلب',
        'plural_label' => 'الطلبات',
        'schema' => [
            'id' => 'المعرف',
            'tracking_id' => 'رقم الطلب في النظام',
            'reference' => 'الرقم المرجعي',
            'payment_method' => 'طريقة الدفع',
            'customer_name' => 'اسم العميل',
            'driver_name' => 'اسم السائق',
            'status' => 'الحالة',
            'base_price' => 'السعر',
            'shipping_price' => 'إجمالي الشحن',
            'total' => 'إجمالي المدفوع',
            'total_discounts' => 'إجمالي الخصومات',
            'is_picked_up' => 'حالة التجميع',
            'delivery_date' => 'تاريخ التسليم',
            'start_time' => 'وقت البدء',
            'end_time' => 'وقت الانتهاء',
            'address' => 'العنوان',
            'customer_phone' => 'هاتف العميل',
            'products' => 'المنتجات',
            'seller_name' => 'البائع',
            'seller_logo' => 'شعار البائع',
            'date_add' => 'تاريخ الإضافة',
            'created_at' => 'تاريخ الإنشاء',
            'updated_at' => 'تاريخ التحديث',
            'delivered_at' => 'تاريخ التوصيل',
            'total_price_of_orders' => 'إجمالي سعر الطلبيات',
            'total_amount' => 'الإجمالي الكلي',
            'total_shipping' => 'إجمالي التوصيل'
        ],
        'form' => [
            'new_status' => 'الحالة الجديدة',
            'select_drivers' => 'إختيار السائقين'
        ],
        'actions' => [
            'assign_driver' => 'تعيين سائق',
            'assign' => 'تعيين',
            'assign_delivery_task' => 'تعيين مهمة توصيل',
            'settle_orders' => [
                'label' => 'تسوية الطلبات',
                'body' => 'هل تريد حقًا تسوية الطلبات المحددة؟ هذا سيؤدي إلى وضع علامة على الطلبات المحددة على أنها مسوية وتسجيل تاريخ التسوية.',
            ],
            'change_status' => 'تغير حالة الطلبية',
            'order_details' => 'تفاصيل الطلبية',
            'withdraw_order_from_driver' => 'سحب الطلبية من السائق'
        ],
        'notification' => [
            'delivery_task_assigned' => 'Delivery Task Assigned'
        ]
    ],

    'users' => [
        'label' => 'المستخدم',
        'plural_label' => 'المستخدمين',
        'schema' => [
            'id' => 'المعرف',
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'created_at' => 'تاريخ الإنشاء',
            'updated_at' => 'تاريخ التحديث',
        ],
    ],
    'activitylogs' => [
        'label' => 'سجل النشاط',
        'plural_label' => 'سجلات النشاط',
        'schema' => [
            'id' => 'الرقم',
            'log_name' => 'نوع السجل',
            'description' => 'الوصف',
            'causer_type' => 'المنفذ',
            'causer_name' => 'إسم المنفذ',
            'causer_id' => 'رقم المنفذ',
            'subject_type' => 'العنصر المستهدف',
            'created_at' => 'تاريخ الإنشاء',
        ]
    ],
    'settlement' => [
        'label' => 'التسوية المالية',
        'plural_label' => 'التسويات المالية',
        'schema' => [
            'id' => 'الرقم',
            'driver_name' => 'السائق',
            'created_at' => 'تاريخ الإنشاء',
        ],
        'view-page' => 'عرض الطلبيات التي تم تسويتها'
    ]
];
