<?php

namespace App\Enums;

enum Permission: string
{
    case ViewUser = 'view user';
    case CreateUser = 'create user';
    case EditUser = 'edit user';
    case DeleteUser = 'delete user';

    case ViewCustomer = 'view customer';
    case CreateCustomer = 'create customer';
    case EditCustomer = 'edit customer';
    case DeleteCustomer = 'delete customer';

    case ViewCategory = 'view category';
    case CreateCategory = 'create category';
    case EditCategory = 'edit category';
    case DeleteCategory = 'delete category';

    case ViewProduct = 'view product';
    case CreateProduct = 'create product';
    case EditProduct = 'edit product';
    case DeleteProduct = 'delete product';

    case ViewProductPurchase = 'view product_purchase';
    case CreateProductPurchase = 'create product_purchase';
    case EditProductPurchase = 'edit product_purchase';
    case DeleteProductPurchase = 'delete product_purchase';

    case ViewSale = 'view sale';
    case CreateSale = 'create sale';
    case EditSale = 'edit sale';
    case DeleteSale = 'delete sale';

    case ViewFinancialTransaction = 'view financial_transaction';
    case CreateFinancialTransaction = 'create financial_transaction';
    case EditFinancialTransaction = 'edit financial_transaction';
    case DeleteFinancialTransaction = 'delete financial_transaction';

    case ViewSetting = 'view setting';
    case CreateSetting = 'create setting';
    case EditSetting = 'edit setting';
    case DeleteSetting = 'delete setting';

    case ViewAccountBalance = 'view account_balance';
    case CreateAccountBalance = 'create account_balance';
    case EditAccountBalance = 'edit account_balance';
    case DeleteAccountBalance = 'delete account_balance';
    case ViewRole = 'view role';
    case CreateRole = 'create role';
    case EditRole = 'edit role';
    case DeleteRole = 'delete role';
    case ViewReport = 'view report';
    case ViewAdmin = 'view admin';
    case ManageChangelog = 'manage changelog';
}
