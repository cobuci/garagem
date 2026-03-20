<?php

return [
    'title'       => 'Atividades Recentes',
    'description' => 'Histórico de transações financeiras e controle de saldo',
    'summary'     => [
        'current_balance' => 'Saldo Atual',
        'total_due'       => 'Total Devido',
        'to_receive'      => 'A Receber',
    ],
    'filters' => [
        'all'               => 'Todas',
        'sale'              => 'Vendas',
        'purchase'          => 'Compras',
        'cancelled_sale'    => 'Cancelamentos',
        'manual_adjustment' => 'Ajustes',
    ],
    'transaction_type' => [
        'sale'              => 'Venda',
        'purchase'          => 'Compra',
        'cancelled_sale'    => 'Venda Cancelada',
        'manual_adjustment' => 'Ajuste Manual',
    ],
    'table' => [
        'date'        => 'Data',
        'type'        => 'Tipo',
        'description' => 'Descrição',
        'amount'      => 'Valor',
        'title'       => 'Histórico de Atividades',
        'no_records'  => 'Nenhuma transação encontrada.',
    ],
    'actions' => [
        'add_balance'    => 'Adicionar Saldo',
        'remove_balance' => 'Remover Saldo',
    ],
    'adjustment_modal' => [
        'title_add'    => 'Adicionar Saldo',
        'title_remove' => 'Remover Saldo',
        'amount'       => 'Valor',
        'description'  => 'Descrição/Motivo',
        'confirm'      => 'Salvar Ajuste',
        'cancel'       => 'Cancelar',
    ],
    'adjustment_success_title'       => 'Saldo Atualizado',
    'adjustment_success_description' => 'A movimentação de saldo foi registrada com sucesso.',
    'cancel_adjustment_title'        => 'Cancelar Ajuste',
    'cancel_adjustment_description'  => 'Tem certeza que deseja cancelar este ajuste? O valor será revertido no saldo.',
    'cancel_success_title'           => 'Ajuste Cancelado',
    'cancel_success_description'     => 'O ajuste foi cancelado e o saldo revertido com sucesso.',
];
