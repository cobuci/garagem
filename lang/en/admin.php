<?php

return [
    'title'    => 'Administration',
    'subtitle' => 'Manage advanced system settings and tools.',
    'tabs'     => [
        'users'  => 'Users',
        'import' => 'Import Legacy',
        'roles'  => 'Roles & Permissions',
    ],
    'users' => [
        'title'    => 'User Management',
        'subtitle' => 'View, create, edit, and delete system users.',
        'fields'   => [
            'name'                      => 'Name',
            'name_placeholder'          => 'Ex: John Doe',
            'email'                     => 'Email',
            'email_placeholder'         => 'Ex: john@email.com',
            'password'                  => 'Password',
            'password_placeholder'      => 'User password',
            'password_placeholder_edit' => 'Leave blank to keep current password',
            'roles'                     => 'Roles',
            'roles_placeholder'         => 'Select roles',
        ],
        'actions' => [
            'label'             => 'Actions',
            'create'            => 'New User',
            'edit'              => 'Edit User',
            'save'              => 'Save',
            'cancel'            => 'Cancel',
            'generate_password' => 'Generate Strong Password',
        ],
        'messages' => [
            'success'            => 'Success',
            'error'              => 'Error',
            'created'            => 'User created successfully.',
            'updated'            => 'User updated successfully.',
            'deleted'            => 'User deleted successfully.',
            'confirm_delete'     => 'Are you sure you want to delete this user?',
            'cannot_delete_self' => 'You cannot delete your own user.',
        ],
    ],
];
