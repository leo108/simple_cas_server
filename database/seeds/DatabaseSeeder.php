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
        \App\Services\User::createOrUpdate('demo', 'Demo User', 'secret', 'demo@demo.com', false);
        \App\Services\User::createOrUpdate('admin', 'Admin User', 'secret', 'admin@demo.com', true);
        \App\Services\Service::createOrUpdate(
            'test',
            [
                'test.com',
                'demo.com',
            ]
        );
    }
}
