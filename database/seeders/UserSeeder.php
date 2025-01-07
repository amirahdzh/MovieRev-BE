<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::where('name', 'admin')->first();

        User::create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'role_id' => $roleAdmin->id,
            'email_verified_at' => Carbon::now()
        ]);
    }
}
