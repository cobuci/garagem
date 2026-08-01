<?php

return [
    'title'    => 'Administração',
    'subtitle' => 'Gerencie as configurações avançadas e ferramentas do sistema.',
    'tabs'     => [
        'users'     => 'Usuários',
        'import'    => 'Importar Legado',
        'roles'     => 'Roles e Permissões',
        'audits'    => 'Log de Auditoria',
        'brand'     => 'Logo da Marca',
        'changelog' => 'Changelog',
    ],
    'brand' => [
        'title'                => 'Logo da marca',
        'subtitle'             => 'Envie o logo usado nos banners promocionais. Aceita apenas PNG (máx. 2 MB).',
        'current'              => 'Logo atual',
        'available_in_banners' => 'Disponível para uso no studio de banners.',
        'empty'                => 'Nenhum logo enviado ainda. Enquanto isso, a opção de logo fica desabilitada nos banners.',
        'fields'               => [
            'upload'  => 'Enviar logo',
            'replace' => 'Substituir logo',
        ],
        'actions' => [
            'save'   => 'Salvar logo',
            'remove' => 'Remover logo',
        ],
        'messages' => [
            'success'        => 'Sucesso',
            'uploaded'       => 'Logo enviado com sucesso.',
            'removed'        => 'Logo removido com sucesso.',
            'confirm_remove' => 'Tem certeza que deseja remover o logo?',
        ],
    ],
    'users' => [
        'title'    => 'Gerenciamento de Usuários',
        'subtitle' => 'Visualize, crie, edite e exclua usuários do sistema.',
        'fields'   => [
            'name'                      => 'Nome',
            'name_placeholder'          => 'Ex: João Silva',
            'email'                     => 'E-mail',
            'email_placeholder'         => 'Ex: joao@email.com',
            'password'                  => 'Senha',
            'password_placeholder'      => 'Senha do usuário',
            'password_placeholder_edit' => 'Deixe em branco para manter a senha atual',
            'roles'                     => 'Cargos (Roles)',
            'roles_placeholder'         => 'Selecione os cargos',
        ],
        'actions' => [
            'label'             => 'Ações',
            'create'            => 'Novo Usuário',
            'edit'              => 'Editar Usuário',
            'save'              => 'Salvar',
            'cancel'            => 'Cancelar',
            'generate_password' => 'Gerar Senha Forte',
        ],
        'messages' => [
            'success'            => 'Sucesso',
            'error'              => 'Erro',
            'created'            => 'Usuário criado com sucesso.',
            'updated'            => 'Usuário atualizado com sucesso.',
            'deleted'            => 'Usuário excluído com sucesso.',
            'confirm_delete'     => 'Tem certeza que deseja excluir este usuário?',
            'cannot_delete_self' => 'Você não pode excluir seu próprio usuário.',
        ],
    ],
];
