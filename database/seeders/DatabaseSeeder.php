<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // create user with name and email and password 
        $PrefumUser = User::create([
            'first_name' => 'perfume',
            'last_name' => 'user',
            'email' => 'prefum@cta.com',
            'password' => bcrypt('12345678'),
            'status' => 'active',
        ]);


        $EmployeesUser = User::create([
            'first_name' => 'Employees',
            'last_name' => 'user',
            'email' => 'employee@cta.com',
            'password' => bcrypt('12345678'),
            'status' => 'active',
        ]);


        $RemainingTree = User::create([
            'first_name' => 'Remaining Tree',
            'last_name' => 'user',
            'email' => 'remainingtree@cta.com',
            'password' => bcrypt('12345678'),
            'status' => 'active',
        ]);

        $user = User::create([
            'first_name' => 'Khaled',
            'last_name' => 'osama',
            'email' => 'khaleddoosama@gmail.com',
            'password' => bcrypt('12345678'),
            'code' => 'KHALEDAMEN',
            'status' => 'active',
        ]);
    }
}
