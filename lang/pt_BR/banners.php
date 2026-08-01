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
        'texts'      => 'Textos livres',
        'export'     => 'Exportar',
        'preview'    => 'Pré-visualização',
    ],

    'hints' => [
        'workflow'        => 'Escolha um modelo, gere o fundo e ajuste os textos.',
        'presets'         => 'Aplique um modelo pronto e depois personalize à vontade.',
        'background'      => 'Escolha um tema e, se quiser, ajuste o clima e a intensidade.',
        'content'         => 'Você também pode tocar direto no texto do banner para editar.',
        'items'           => 'Itens sem nome não aparecem no banner final.',
        'texts'           => 'Adicione textos com cor e fonte próprias e arraste na pré-visualização para posicionar.',
        'tap_to_edit'     => 'Toque no texto para editar',
        'drag_to_reorder' => 'Arraste para reordenar',
        'mood'            => 'Define a iluminação geral do fundo gerado pela IA.',
        'intensity'       => 'Controla o quanto os elementos do fundo se destacam.',
        'resolution'      => 'Multiplica o tamanho do arquivo exportado. 2x e 3x ficam mais nítidos.',
    ],

    'mood_tips' => [
        'dark'  => 'Fundo escuro e sofisticado — ideal para promoções noturnas.',
        'light' => 'Iluminação clara e arejada — bom para petiscos e promoções do dia.',
        'night' => 'Ambiente noturno com pontos de luz — combina com happy hour.',
    ],

    'intensity_tips' => [
        'soft' => 'Elementos discretos e desfocados — o texto ganha mais atenção.',
        'bold' => 'Cores vivas e elementos marcantes — mais impacto visual.',
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
        'fathers_day' => [
            'label'       => 'Dia dos Pais',
            'description' => 'Tons azuis e dourados, clima carinhoso',
        ],
        'mothers_day' => [
            'label'       => 'Dia das Mães',
            'description' => 'Flores suaves e tons pastel',
        ],
        'christmas' => [
            'label'       => 'Natal',
            'description' => 'Luzes douradas e clima natalino',
        ],
        'easter' => [
            'label'       => 'Páscoa',
            'description' => 'Pastéis claros e clima primaveril',
        ],
        'new_year' => [
            'label'       => 'Ano Novo',
            'description' => 'Fogos desfocados e brilho dourado',
        ],
        'valentines' => [
            'label'       => 'Dia dos Namorados',
            'description' => 'Rosa e vermelho românticos',
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
        'title_font'       => 'Fonte do título',
        'text_font'        => 'Fonte do texto',
        'text_content'     => 'Texto',
        'font'             => 'Fonte',
        'size'             => 'Tamanho',
        'color'            => 'Cor',
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
        'save_changes'        => 'Salvar alterações',
        'cancel'              => 'Cancelar',
        'delete'              => 'Excluir',
        'back'                => 'Voltar',
        'add_item'            => 'Adicionar item',
        'add_text'            => 'Adicionar texto',
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
        'unsaved_changes'       => 'Alterações não salvas',
        'save_to_keep'          => 'Salve para não perder as mudanças',
        'discard_unsaved'       => 'Há alterações não salvas. Deseja sair mesmo assim?',
    ],
];
