<?php

return [
    'title'       => 'Contas a Pagar',
    'description' => 'Gerenciamento de pagamentos de fornecedores e produtos',
    'summary'     => [
        'total_due'  => 'Total Devido',
        'overdue'    => 'Total Vencido',
        'next_month' => 'Próximo Mês',
    ],
    'filters' => [
        'search_placeholder' => 'Buscar produto...',
        'pending'            => 'Pendentes',
        'paid'               => 'Pagos',
        'all'                => 'Todos',
    ],
    'table' => [
        'due_date'    => 'Vencimento',
        'product'     => 'Produto',
        'quantity'    => 'Qtd',
        'total_value' => 'Valor Total',
        'status'      => 'Status',
        'paid_at'     => 'Pago em :date',
        'overdue'     => 'Vencido',
        'pending'     => 'Pendente',
        'no_records'  => 'Nenhuma conta encontrada.',
    ],
    'actions' => [
        'pay'                         => 'Pagar',
        'confirm_payment'             => 'Confirmar pagamento desta conta?',
        'payment_success_title'       => 'Contas a Pagar',
        'payment_success_description' => 'Pagamento registrado com sucesso!',
        'confirm_modal'               => [
            'title'       => 'Confirmar Pagamento',
            'description' => 'Você está prestes a registrar o pagamento para:',
            'product'     => 'Produto',
            'value'       => 'Valor',
            'due_date'    => 'Vencimento',
            'confirm'     => 'Confirmar Pagamento',
        ],
    ],
];
