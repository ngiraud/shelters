<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShelterSeeder::class,
            UserSeeder::class,
            AnimalSeeder::class,
        ]);

        Artisan::call('passport:client', [
            '--password' => true,
            '--name' => "Shelter's app",
            '--provider' => 'users',
        ]);
    }
}
