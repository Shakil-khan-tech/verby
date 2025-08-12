<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'view_devices']);
        Permission::create(['name' => 'manage_devices']);
        Permission::create(['name' => 'view_employees']);
        Permission::create(['name' => 'manage_employees']);
        Permission::create(['name' => 'view_vacations']);
        Permission::create(['name' => 'manage_vacations']);
        Permission::create(['name' => 'view_records']);
        Permission::create(['name' => 'manage_records']);
        Permission::create(['name' => 'view_holidays']);
        Permission::create(['name' => 'manage_holidays']);
        Permission::create(['name' => 'manage_plans']);
        Permission::create(['name' => 'manage_calendars']);
        Permission::create(['name' => 'manage_payrolls']);
        Permission::create(['name' => 'manage_daily_reports']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'super_admin']);
        $role1->givePermissionTo('view_users');
        $role1->givePermissionTo('manage_users');
        $role1->givePermissionTo('view_devices');
        $role1->givePermissionTo('manage_devices');
        $role1->givePermissionTo('view_employees');
        $role1->givePermissionTo('manage_employees');
        $role1->givePermissionTo('view_vacations');
        $role1->givePermissionTo('manage_vacations');
        $role1->givePermissionTo('view_records');
        $role1->givePermissionTo('manage_records');
        $role1->givePermissionTo('view_holidays');
        $role1->givePermissionTo('manage_holidays');
        $role1->givePermissionTo('manage_plans');
        $role1->givePermissionTo('manage_calendars');
        $role1->givePermissionTo('manage_payrolls');
        $role1->givePermissionTo('manage_daily_reports');

        $role2 = Role::create(['name' => 'admin']);
        $role2->givePermissionTo('view_users');
        $role2->givePermissionTo('manage_users');
        $role2->givePermissionTo('view_devices');
        $role2->givePermissionTo('manage_devices');
        $role2->givePermissionTo('view_employees');
        $role2->givePermissionTo('manage_employees');
        $role2->givePermissionTo('view_vacations');
        $role2->givePermissionTo('manage_vacations');
        $role2->givePermissionTo('view_records');
        $role2->givePermissionTo('manage_records');
        $role2->givePermissionTo('view_holidays');
        $role2->givePermissionTo('manage_holidays');
        $role2->givePermissionTo('manage_plans');
        $role2->givePermissionTo('manage_calendars');
        $role2->givePermissionTo('manage_payrolls');
        $role2->givePermissionTo('manage_daily_reports');

        $role3 = Role::create(['name' => 'default']);
        $role3->givePermissionTo('view_devices');
        $role3->givePermissionTo('view_employees');
        $role3->givePermissionTo('manage_employees');
        $role3->givePermissionTo('view_vacations');
        $role3->givePermissionTo('view_records');
        $role3->givePermissionTo('view_holidays');
        $role3->givePermissionTo('manage_plans');
        $role3->givePermissionTo('manage_calendars');
        $role3->givePermissionTo('manage_payrolls');
        $role3->givePermissionTo('manage_daily_reports');


        // gets all permissions via Gate::before rule; see AuthServiceProvider

        $user = \App\Models\User::where('email', 'jetmir.amiti@gmail.com')->first();
        $user->assignRole($role1);

        $user = \App\Models\User::where('email', 'daut.ahmeti@aaab.ch')->first();
        $user->assignRole($role2);

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Example User',
            'email' => 'example@verby.ch',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'is_device' => 0,
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role3);


    }
}
