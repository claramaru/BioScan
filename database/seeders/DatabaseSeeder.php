<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Schema::hasTable('usuario')) {
            User::query()->firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'nombre' => 'Test',
                    'apellidos' => 'User',
                    'password' => 'password',
                    'id_rol' => 1,
                ]
            );
        }

        if (
            Schema::hasTable('animal')
            && Schema::hasTable('cebadero')
            && Schema::hasColumn('animal', 'raza')
        ) {
            $this->call([
                AnimalBreedSeeder::class,
            ]);
        }
    }
}
