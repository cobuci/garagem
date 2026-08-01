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
        'content'    => 'Conteúdo',
        'items'      => 'Itens e Preços',
        'background' => 'Fundo com IA',
        'export'     => 'Exportar',
        'preview'    => 'Pré-visualização',
    ],

    'fields' => [
        'name'                          => 'Nome',
        'name_placeholder'              => 'Ex: Promoção de Agosto',
        'format'                        => 'Formato',
        'created_by'                    => 'Criado por',
        'updated_at'                    => 'Atualizado',
        'title'                         => 'Título',
        'subtitle'                      => 'Subtítulo',
        'footer'                        => 'Rodapé',
        'background_color'              => 'Fundo',
        'accent_color'                  => 'Destaque',
        'text_color'                    => 'Texto',
        'item_name'                     => 'Item',
        'item_note'                     => 'Nota',
        'item_price'                    => 'Preço',
        'background_prompt'             => 'Descreva o fundo desejado',
        'background_prompt_placeholder' => 'Ex: fundo escuro com detalhes de oficina mecânica, tons de azul, estilo moderno',
        'resolution'                    => 'Resolução',
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
        'background_generating' => 'Gerando fundo com IA, aguarde...',
        'background_failed'     => 'Falha ao gerar o fundo. Tente novamente.',
        'export_generating'     => 'Exportando em alta qualidade, aguarde...',
        'export_failed'         => 'Falha ao exportar. Tente novamente.',
    ],
];
