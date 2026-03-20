<?php

return [
    'title'    => 'Configurações Gerais',
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
            'title'                  => 'Informações Gerais',
            'description'            => 'Informações básicas sobre o seu negócio.',
            'store_name'             => 'Nome da Loja',
            'store_name_placeholder' => 'Ex: Garagem das Bebidas',
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
];
