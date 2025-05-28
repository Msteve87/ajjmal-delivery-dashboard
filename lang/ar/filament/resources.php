<?php

return [
    'driver' => [
        'label' => 'السائق',
        'plural_label' => 'السائقين',
        'schema' => [
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
    ],
    'order' => [
        'label' => 'الطلب',
        'plural_label' => 'الطلبات',
        'schema' => [
            'jm_order_id' => 'معرف الطلب في النظام',
            'reference' => 'الرقم المرجعي',
            'payment_method' => 'طريقة الدفع',
            'customer_name' => 'اسم العميل',
            'driver_name' => 'اسم السائق',
            'status' => 'الحالة',
            'total_shipping' => 'إجمالي الشحن',
            'total_paid' => 'إجمالي المدفوع',
            'delivery_date' => 'تاريخ التسليم',
            'start_time' => 'وقت البدء',
            'end_time' => 'وقت الانتهاء',
            'address' => 'العنوان',
            'customer_phone' => 'هاتف العميل',
            'products' => 'المنتجات',
        ],
    ],
];
