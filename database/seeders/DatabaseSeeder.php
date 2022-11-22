<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         User::create([
             'is_admin' => 1,
             'name' => 'Root',
             'login' => 'root',
             'password' => bcrypt('root1337'),
         ]);
    }
}
