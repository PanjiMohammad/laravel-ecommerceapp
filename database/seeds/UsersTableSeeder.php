<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'panji',
            'email' => 'panji@admin.com',
            'password' => bcrypt('admin')
        ]);
    }
}
