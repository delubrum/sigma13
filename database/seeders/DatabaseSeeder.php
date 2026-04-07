<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Felipe',
            'email' => 'lfelipecorreah@gmail.com',
            'permissions' => '["29","102","82","34","147","140","53","120","123","121","128","85","139","93","151","75","9","103","142","143","54","116","86","154","155","156","35","44","68","69","30","152","115","158","159","160","97","105","164","110","165","166","134","150","163","162","157"]',
        ]);
    }
}
