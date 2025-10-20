<?php

return [
    'driver' => [
        'label' => 'Driver',
        'plural_label' => 'Drivers',
        'schema' => [
            'id' => 'ID',
            'name' => 'Name',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'driver_type' => 'Driver Type',
            'gender' => 'Gender',
            'criminal_case' => 'Criminal Case',
            'delivery_status' => 'Delivery Status',
            'passport_number' => 'Passport Number',
            'national_number' => 'National Number',
            'date_of_birth' => 'Date of Birth',
            'status' => 'Status',
            'is_active' => 'Is Active',
        ],
        'form' => [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'attachments' => 'Attachments',
            'vehicle_registration' => 'Vehicle Registration',
            'vehicle_insurance' => 'Vehicle Insurance',
            'vehicle_license' => 'Roaming License Image',
            'license_no' => 'License Number',
            'passport_no' => 'Passport Number',
            'criminal_case' => 'Criminal Case',
            'national_no' => 'National Number',
            'vehicle_insurance_attachment' => 'Vehicle Insurance Image',
            'vehicle_registration_attachment' => 'Vehicle Registration Image',
            'vehicle_license_attachment' => 'Vehicle License Image',
            'license_attachment' => 'License Image',
            'passport_attachment' => 'Passport Image',
            'criminal_case_attachment' => 'Criminal Case Image',
            'documents' => 'Driver Documents'
        ],
        'view-page' => [
            'label' => 'View Driver',
            'tables' => [
                'delivered_sub_orders' => 'Delivered Orders',
            ]
        ],
    ],
    'order' => [
        'label' => 'Main Order',
        'plural_label' => 'Main Orders',
        'schema' => [
            'id' => 'ID',
            'jm_order_id' => 'JM Order ID',
            'reference' => 'Reference Number',
            'payment_method' => 'Payment Method',
            'customer_name' => 'Customer Name',
            'driver_name' => 'Driver Name',
            'status' => 'Status',
            'total_shipping' => 'Total Shipping',
            'price' => 'Price',
            'total_paid' => 'Total Paid',
            'delivery_date' => 'Delivery Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'address' => 'Address',
            'customer_phone' => 'Customer Phone',
            'products' => 'Products',
        ],
        'actions' => [
            'assign_driver' => 'Assign Driver',
        ],
    ],
    'sub_order' => [
        'label' => 'Order',
        'plural_label' => 'Orders',
        'schema' => [
            'id' => 'Id',
            'tracking_id' => 'Tracking ID',
            'reference' => 'Reference',
            'payment_method' => 'Payment Method',
            'customer_name' => 'Customer Name',
            'driver_name' => 'Driver Name',
            'status' => 'Status',
            'base_price' => 'Price',
            'shipping_price' => 'Shipping Price',
            'total' => 'Total Paid',
            'total_discounts' => 'Total Discounts',
            'is_picked_up' => 'Is Picked Up',
            'delivery_date' => 'Delivery Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'address' => 'Address',
            'customer_phone' => 'Customer Phone',
            'products' => 'Products',
            'seller_name' => 'Seller',
            'seller_logo' => 'Seller Logo',
            'date_add' => 'Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'delivered_at' => 'Delivered At',
            'total_price_of_orders' => 'Total Price Of Orders',
            'total_amount' => 'Total Amount',
            'total_shipping' => 'Total Shipping'
        ],
        'form' => [
            'new_status' => 'New Status',
            'select_drivers' => 'Select Drivers'
        ],
        'actions' => [
            'assign_driver' => 'Assign Driver',
            'assign' => 'Assign',
            'assign_delivery_task' => 'Assign Delivery Task',
            'settle_orders' => [
                'label' => 'Settle Orders',
                'body' => 'This will mark the selected orders as settled and record the settlement date.',
            ],
            'change_status' => 'Change Status',
            'order_details' => 'Order Details',
            'withdraw_order_from_driver' => 'Withdraw Order'
        ],
        'notification' => [
            'delivery_task_assigned' => 'Delivery Task Assigned'
        ]
    ],

    'users' => [
        'label' => 'User',
        'plural_label' => 'Users',
        'schema' => [
            'id' => 'Id',
            'name' => 'Name',
            'email' => 'Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ]
    ],
    'activitylogs' => [
        'label' => 'Activity Log',
        'plural_label' => 'Activity Logs',
        'schema' => [
            'id' => 'ID',
            'log_name' => 'Log Type',
            'description' => 'Description',
            'causer_type' => 'Causer Type',
            'causer_name' => 'Causer Name',
            'causer_id' => 'Causer ID',
            'subject_type' => 'Subject Type',
            'created_at' => 'Created At',
        ],
    ],
];
