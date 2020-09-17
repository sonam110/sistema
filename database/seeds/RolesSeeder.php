<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');

        Permission::create(['name' => 'edit-profile', 'guard_name' => 'web']);

        Permission::create(['name' => 'employee-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee-create', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee-edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee-view', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee-delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee-action', 'guard_name' => 'web']);

        Permission::create(['name' => 'supplier-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'supplier-create', 'guard_name' => 'web']);
        Permission::create(['name' => 'supplier-edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'supplier-view', 'guard_name' => 'web']);
        Permission::create(['name' => 'supplier-delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'supplier-action', 'guard_name' => 'web']);

        Permission::create(['name' => 'product-list', 'guard_name' => 'web']);

        Permission::create(['name' => 'purchase-order-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-order-create', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-order-view', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-order-delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-order-download', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-order-action', 'guard_name' => 'web']);

        Permission::create(['name' => 'purchase-order-received-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-order-receiving', 'guard_name' => 'web']);

        Permission::create(['name' => 'purchase-order-return-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-order-return', 'guard_name' => 'web']);

        Permission::create(['name' => 'customer-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'customer-create', 'guard_name' => 'web']);
        Permission::create(['name' => 'customer-edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'customer-view', 'guard_name' => 'web']);
        Permission::create(['name' => 'customer-delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'customer-action', 'guard_name' => 'web']);

        Permission::create(['name' => 'sales-order-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'sales-order-create', 'guard_name' => 'web']);
        Permission::create(['name' => 'sales-order-view', 'guard_name' => 'web']);
        Permission::create(['name' => 'sales-order-download', 'guard_name' => 'web']);
        Permission::create(['name' => 'sales-order-adction', 'guard_name' => 'web']);

        Permission::create(['name' => 'sales-order-return-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'sales-order-return', 'guard_name' => 'web']);

        Permission::create(['name' => 'direct-sales-return', 'guard_name' => 'web']);
        Permission::create(['name' => 'direct-purchase-return', 'guard_name' => 'web']);

        Permission::create(['name' => 'installment-order-list', 'guard_name' => 'web']);
        Permission::create(['name' => 'installment-paid-history', 'guard_name' => 'web']);
        Permission::create(['name' => 'installment-receive', 'guard_name' => 'web']);
        Permission::create(['name' => 'installment-action', 'guard_name' => 'web']);

        Permission::create(['name' => 'sales-report', 'guard_name' => 'web']);
        Permission::create(['name' => 'export-sales-report', 'guard_name' => 'web']);
        Permission::create(['name' => 'purchase-report', 'guard_name' => 'web']);
        Permission::create(['name' => 'export-purchase-report', 'guard_name' => 'web']);
        Permission::create(['name' => 'short-stock-item-report', 'guard_name' => 'web']);


        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $adminAssignRole = User::first();
        $adminAssignRole->userType = '0';
        $adminAssignRole->save();
        $adminAssignRole->assignRole('admin');

        $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'web']);
        $employeePermission = 'edit-profile product-list customer-list customer-create customer-view sales-order-list sales-order-create sales-order-view sales-order-download sales-order-return-list sales-order-return installment-order-list installment-paid-history installment-receive installment-action sales-report short-stock-item-report';
        foreach (explode(' ', $employeePermission) as $key => $value) {
            $employeeRole->givePermissionTo($value);
        }

    }
}
