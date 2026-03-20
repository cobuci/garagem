<?php

return [
    'title'       => 'Bills Payable',
    'description' => 'Management of supplier and product payments',
    'summary'     => [
        'total_due'  => 'Total Due',
        'overdue'    => 'Total Overdue',
        'next_month' => 'Next Month',
    ],
    'filters' => [
        'search_placeholder' => 'Search product...',
        'pending'            => 'Pending',
        'paid'               => 'Paid',
        'all'                => 'All',
    ],
    'table' => [
        'due_date'    => 'Due Date',
        'product'     => 'Product',
        'quantity'    => 'Qty',
        'total_value' => 'Total Value',
        'status'      => 'Status',
        'paid_at'     => 'Paid on :date',
        'overdue'     => 'Overdue',
        'pending'     => 'Pending',
        'no_records'  => 'No bills found.',
    ],
    'actions' => [
        'pay'                         => 'Pay',
        'confirm_payment'             => 'Confirm payment for this bill?',
        'payment_success_title'       => 'Bills Payable',
        'payment_success_description' => 'Payment recorded successfully!',
        'confirm_modal'               => [
            'title'       => 'Confirm Payment',
            'description' => 'You are about to record the payment for:',
            'product'     => 'Product',
            'value'       => 'Value',
            'due_date'    => 'Due Date',
            'confirm'     => 'Confirm Payment',
        ],
    ],
];
