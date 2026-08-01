<?php

return [
    'title'    => 'Banners',
    'subtitle' => 'Crie banners promocionais com fundo gerado por IA e exporte em alta qualidade.',
    'empty'    => 'Nenhum banner criado ainda.',

    'formats' => [
        'stories' => 'Stories (1080 × 1920)',
        'post'    => 'Post (1080 × 1350)',
        'square'  => 'Quadrado (1080 × 1080)',
    ],

    'sections' => [
        'presets'    => 'Comece com um modelo',
        'background' => 'Fundo com IA',
        'logo'       => 'Logo',
        'content'    => 'Conteúdo',
        'items'      => 'Itens e Preços',
        'export'     => 'Exportar',
        'preview'    => 'Pré-visualização',
    ],

    'hints' => [
        'workflow'    => 'Escolha um modelo, gere o fundo e ajuste os textos.',
        'presets'     => 'Aplique um modelo pronto e depois personalize à vontade.',
        'background'  => 'Escolha um tema e, se quiser, ajuste o clima e a intensidade.',
        'content'     => 'Você também pode tocar direto no texto do banner para editar.',
        'items'       => 'Itens sem nome não aparecem no banner final.',
        'tap_to_edit' => 'Toque no texto para editar',
    ],

    'presets' => [
        'barbecue' => 'Espetinhos',
        'drinks'   => 'Bebidas geladas',
        'snacks'   => 'Petiscos',
    ],

    'themes' => [
        'barbecue' => [
            'label'       => 'Churrasco',
            'description' => 'Brasas, fumaça e clima de espetinho',
        ],
        'cold_drinks' => [
            'label'       => 'Bebidas geladas',
            'description' => 'Garrafas geladas e tons azuis',
        ],
        'snacks' => [
            'label'       => 'Petiscos',
            'description' => 'Porções em tons quentes e dourados',
        ],
        'daily_promo' => [
            'label'       => 'Promo do dia',
            'description' => 'Fundo escuro, limpo e elegante',
        ],
        'happy_hour' => [
            'label'       => 'Happy hour',
            'description' => 'Luzes desfocadas e clima festivo',
        ],
        'brand_minimal' => [
            'label'       => 'Minimalista',
            'description' => 'Gradiente azul com a cara da marca',
        ],
    ],

    'moods' => [
        'dark'  => 'Escuro',
        'light' => 'Claro',
        'night' => 'Noturno',
    ],

    'intensities' => [
        'soft' => 'Suave',
        'bold' => 'Marcante',
    ],

    'logo_positions' => [
        'top'    => 'Topo',
        'bottom' => 'Rodapé',
    ],

    'logo_aligns' => [
        'left'   => 'Esquerda',
        'center' => 'Centro',
        'right'  => 'Direita',
    ],

    'palettes' => [
        'brand_dark'  => 'Marca (escuro)',
        'brand_light' => 'Marca (claro)',
    ],

    'logo_sizes' => [
        'small'  => 'Pequeno',
        'medium' => 'Médio',
        'large'  => 'Grande',
    ],

    'fields' => [
        'name'             => 'Nome',
        'name_placeholder' => 'Ex: Promoção de Agosto',
        'format'           => 'Formato',
        'created_by'       => 'Criado por',
        'updated_at'       => 'Atualizado',
        'title'            => 'Título',
        'subtitle'         => 'Subtítulo',
        'footer'           => 'Rodapé',
        'background_color' => 'Fundo',
        'accent_color'     => 'Destaque',
        'text_color'       => 'Texto',
        'show_logo'        => 'Mostrar logo no banner',
        'logo_position'    => 'Posição do logo',
        'logo_align'       => 'Alinhamento do logo',
        'logo_size'        => 'Tamanho do logo',
        'brand_colors'     => 'Cores da marca',
        'mood'             => 'Clima',
        'intensity'        => 'Intensidade',
        'item_name'        => 'Item',
        'item_note'        => 'Nota',
        'item_price'       => 'Preço',
        'resolution'       => 'Resolução',
    ],

    'actions' => [
        'label'               => 'Ações',
        'create'              => 'Novo Banner',
        'save'                => 'Salvar',
        'cancel'              => 'Cancelar',
        'delete'              => 'Excluir',
        'back'                => 'Voltar',
        'add_item'            => 'Adicionar item',
        'generate_background' => 'Gerar fundo com IA',
        'remove_background'   => 'Remover fundo',
        'export'              => 'Exportar',
        'download_png'        => 'Baixar PNG',
        'download_pdf'        => 'Baixar PDF',
    ],

    'messages' => [
        'success'               => 'Sucesso!',
        'saved'                 => 'Banner salvo.',
        'deleted'               => 'Banner excluído.',
        'delete_title'          => 'Excluir banner?',
        'delete_description'    => 'Essa ação não pode ser desfeita.',
        'theme_required'        => 'Escolha um tema para gerar o fundo.',
        'background_generating' => 'Gerando fundo com IA, aguarde...',
        'background_failed'     => 'Falha ao gerar o fundo. Tente novamente.',
        'export_generating'     => 'Exportando em alta qualidade, aguarde...',
        'export_failed'         => 'Falha ao exportar. Tente novamente.',
    ],
];
