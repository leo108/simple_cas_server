<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Services\User::create('demo', 'Demo User', 'secret', 'demo@demo.com', false);
        \App\Services\User::create('admin', 'Admin User', 'secret', 'admin@demo.com', true);
    }
}
