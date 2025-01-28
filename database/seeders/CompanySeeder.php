<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'fullName' =>  'القدموس',
            'email' => 'kadmos@gmail.com',
            'password' => bcrypt('12345678q'),
        ]);
        $admin = User::find(1);
        $role = Role::where('name', 'admin')->first();
        $admin->assignRole($role);

        DB::table('users')->insert([
            'fullName' =>  'الاسطورة',
            'email' => 'astora@gmail.com',
            'password' => bcrypt('12345678q'),
        ]);

        $admin = User::find(2);
        $role = Role::where('name', 'admin')->first();
        $admin->assignRole($role);

        DB::table('users')->insert([
            'fullName' =>  'الحسن',
            'email' => 'alhasan@gmail.com',
            'password' => bcrypt('12345678q'),
        ]);

        $admin = User::find(3);
        $role = Role::where('name', 'admin')->first();
        $admin->assignRole($role);
    }
}
