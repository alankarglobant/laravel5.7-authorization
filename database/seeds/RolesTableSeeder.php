<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('TRUNCATE roles;');
        $roles = [
            [
                'name' => 'Vendor',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ],
            [
                'name' => 'User',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ],
            [
                'name' => 'Staff',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ]
        ];

        foreach($roles as $role) {
            Role::create($role);
        }
    }
}
