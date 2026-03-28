<?php

return [
    'events' => [
        'created'  => 'Criado',
        'updated'  => 'Atualizado',
        'deleted'  => 'Deletado',
        'restored' => 'Restaurado',
    ],
    'filters' => [
        'all_events' => 'Todos',
        'all_models' => 'Todos os models',
        'all_users'  => 'Todos os usuários',
    ],
    'table' => [
        'date'      => 'Data',
        'event'     => 'Evento',
        'model'     => 'Model',
        'user'      => 'Usuário',
        'changes'   => 'Alterações',
        'view_diff' => 'Ver diff',
        'system'    => 'Sistema',
        'empty'     => 'Nenhum registro de auditoria encontrado.',
        'fields'    => ':count campo|:count campos',
    ],
    'modal' => [
        'title'      => 'Detalhes da Auditoria',
        'model'      => 'Model',
        'event'      => 'Evento',
        'user'       => 'Usuário',
        'date'       => 'Data',
        'field'      => 'Campo',
        'old_value'  => 'Valor anterior',
        'new_value'  => 'Novo valor',
        'no_changes' => 'Nenhuma alteração de campo registrada para este evento.',
        'close'      => 'Fechar',
    ],
];
