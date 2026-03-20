<?php

return [
    'title'       => 'Recent Activities',
    'description' => 'Financial transaction history and balance control',
    'summary'     => [
        'current_balance' => 'Current Balance',
        'total_due'       => 'Total Due',
        'to_receive'      => 'To Receive',
    ],
    'filters' => [
        'all'               => 'All',
        'sale'              => 'Sales',
        'purchase'          => 'Purchases',
        'cancelled_sale'    => 'Cancellations',
        'manual_adjustment' => 'Adjustments',
    ],
    'transaction_type' => [
        'sale'              => 'Sale',
        'purchase'          => 'Purchase',
        'cancelled_sale'    => 'Cancelled Sale',
        'manual_adjustment' => 'Manual Adjustment',
    ],
    'table' => [
        'date'        => 'Date',
        'type'        => 'Type',
        'description' => 'Description',
        'amount'      => 'Amount',
        'no_records'  => 'No transactions found.',
    ],
    'actions' => [
        'add_balance'    => 'Add Balance',
        'remove_balance' => 'Remove Balance',
    ],
    'adjustment_modal' => [
        'title_add'    => 'Add Balance',
        'title_remove' => 'Remove Balance',
        'amount'       => 'Amount (in cents)',
        'description'  => 'Description/Reason',
        'confirm'      => 'Save Adjustment',
        'cancel'       => 'Cancel',
    ],
    'adjustment_success_title'       => 'Balance Updated',
    'adjustment_success_description' => 'The balance movement was successfully recorded.',
];
