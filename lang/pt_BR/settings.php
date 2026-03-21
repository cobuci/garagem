<?php

return [
    'title'    => 'Configurações Gerais',
    'settings' => 'Configurações',
    'subtitle' => 'Gerencie as configurações e informações do seu estabelecimento.',
    'sections' => [
        'fees' => [
            'title'       => 'Taxas de Cartão',
            'description' => 'Configure as taxas cobradas pelas operadoras de cartão.',
            'credit_fee'  => 'Taxa de Crédito (%)',
            'debit_fee'   => 'Taxa de Débito (%)',
        ],
        'address' => [
            'title'                => 'Endereço do Estabelecimento',
            'description'          => 'Estas informações serão usadas para serviços de localização e clima.',
            'street'               => 'Endereço',
            'street_placeholder'   => 'Rua, número, bairro...',
            'city'                 => 'Cidade',
            'state'                => 'Estado',
            'zip_code'             => 'CEP',
            'zip_code_placeholder' => '00000-000',
        ],
        'general' => [
            'title'                          => 'Informações Gerais',
            'description'                    => 'Informações básicas sobre o seu negócio.',
            'store_name'                     => 'Nome da Loja',
            'store_name_placeholder'         => 'Ex: Garagem das Bebidas',
            'skipped_categories'             => 'Categorias Ignoradas',
            'skipped_categories_placeholder' => 'Selecione as categorias que não entrarão nos cálculos',
        ],
        'preferences' => [
            'title'       => 'Preferências',
            'description' => 'Personalize sua experiência no sistema.',
            'language'    => 'Idioma',
        ],
    ],
    'actions' => [
        'save'    => 'Salvar',
        'saving'  => 'Salvando...',
        'success' => 'Configurações atualizadas!',
    ],
    'import_legacy_data'         => 'Importar Dados Legados',
    'import_legacy_description'  => 'Faça upload de um arquivo SQL do sistema antigo para importar categorias, produtos e clientes.',
    'sql_file'                   => 'Arquivo SQL',
    'sql_file_hint'              => 'Selecione o arquivo .sql exportado do banco de dados antigo.',
    'import_now'                 => 'Importar Agora',
    'import_notice'              => 'A importação é processada em segundo plano. O tempo de conclusão depende do tamanho do arquivo.',
    'import_started'             => 'Importação Iniciada',
    'import_started_description' => 'O arquivo foi recebido e está sendo processado. Os dados aparecerão no sistema em breve.',
];
