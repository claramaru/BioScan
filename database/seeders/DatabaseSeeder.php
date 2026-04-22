<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CebaderoSeeder::class,
            RolSeeder::class,
            PrivilegioSeeder::class,
            RolPrivilegioSeeder::class,
            UsuarioSeeder::class,
            AnimalSeeder::class,
            AlimentacionSeeder::class,
            FichaMedicaSeeder::class,
        ]);
    }
}
