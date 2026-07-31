<?php

return [
    'title'       => 'Categories',
    'subtitle'    => 'Manage inventory categories. Drag to reorder.',
    'empty'       => 'No categories registered.',
    'delete_word' => 'delete',

    'fields' => [
        'name'             => 'Name',
        'name_placeholder' => 'E.g. Beverages',
        'sort_order'       => 'Order',
        'products_count'   => 'Products',
    ],

    'actions' => [
        'label'   => 'Actions',
        'create'  => 'New Category',
        'edit'    => 'Edit Category',
        'delete'  => 'Delete Category',
        'save'    => 'Save',
        'cancel'  => 'Cancel',
        'reorder' => 'Drag to reorder',
    ],

    'messages' => [
        'success'                     => 'Success',
        'error'                       => 'Error',
        'created'                     => 'Category created successfully!',
        'updated'                     => 'Category updated successfully!',
        'deleted'                     => 'Category deleted successfully!',
        'delete_confirm'              => 'Are you sure you want to delete the category :category?',
        'delete_instruction'          => 'To confirm deletion, type the word :word below.',
        'delete_incorrect'            => 'The confirmation word is incorrect.',
        'cannot_delete_with_products' => 'Categories with linked products cannot be deleted.',
    ],
];
