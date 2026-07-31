<?php

return [
    'title'       => 'Categorias',
    'subtitle'    => 'Gerencie as categorias do estoque. Arraste para reordenar.',
    'empty'       => 'Nenhuma categoria cadastrada.',
    'delete_word' => 'excluir',

    'fields' => [
        'name'             => 'Nome',
        'name_placeholder' => 'Ex: Bebidas',
        'sort_order'       => 'Ordem',
        'products_count'   => 'Produtos',
    ],

    'actions' => [
        'label'   => 'Ações',
        'create'  => 'Nova Categoria',
        'edit'    => 'Editar Categoria',
        'delete'  => 'Excluir Categoria',
        'save'    => 'Salvar',
        'cancel'  => 'Cancelar',
        'reorder' => 'Arrastar para reordenar',
    ],

    'messages' => [
        'success'                     => 'Sucesso',
        'error'                       => 'Erro',
        'created'                     => 'Categoria criada com sucesso!',
        'updated'                     => 'Categoria atualizada com sucesso!',
        'deleted'                     => 'Categoria excluída com sucesso!',
        'delete_confirm'              => 'Tem certeza que deseja excluir a categoria :category?',
        'delete_instruction'          => 'Para confirmar a exclusão, digite a palavra :word abaixo.',
        'delete_incorrect'            => 'A palavra de confirmação está incorreta.',
        'cannot_delete_with_products' => 'Não é possível excluir categorias que possuem produtos atrelados.',
    ],
];
