<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuario')->upsert([
            [
                'id_usuario' => 1,
                'nombre' => 'Admin',
                'apellidos' => 'BioScan',
                'email' => 'admin@bioscan.com',
                'id_rol' => 1,
                'password' => '$2y$12$asT8RUq1F4jPjwHH6bY0..zF0v8kfxcy78KXs0VeBjJBjb77AwvQG',
                'remember_token' => null,
                'email_verified_at' => null,
            ],
            [
                'id_usuario' => 2,
                'nombre' => 'Juan',
                'apellidos' => 'Pérez García',
                'email' => 'juanperezg@bioscan.com',
                'id_rol' => 1,
                'password' => '$2y$12$rIvz7woKJPXC0h1sEXER5ulcgMgqFJCP/gkzfJx1pp4Qab1tGSPZy',
                'remember_token' => null,
                'email_verified_at' => null,
            ],
            [
                'id_usuario' => 3,
                'nombre' => 'Marta',
                'apellidos' => 'López Sánchez',
                'email' => 'martalopezs@bioscan.com',
                'id_rol' => 2,
                'password' => '$2y$12$DqVA6wEOSEMZ5bKe8CzgF.0jf1pdZDT7FfquGm8wblhMUbGyA3uJO',
                'remember_token' => null,
                'email_verified_at' => null,
            ],
            [
                'id_usuario' => 4,
                'nombre' => 'Carlos',
                'apellidos' => 'Ruiz Martínez',
                'email' => 'carlosruizm@bioscan.com',
                'id_rol' => 3,
                'password' => '$2y$12$QjJSEAoKrlIxYrE9UTjw5eNLHMuAMyod4hwDYXXxoAKRH9Y9hXP2O',
                'remember_token' => null,
                'email_verified_at' => null,
            ],
            [
                'id_usuario' => 5,
                'nombre' => 'Laura',
                'apellidos' => 'Gómez Navarro',
                'email' => 'lauragomezn@bioscan.com',
                'id_rol' => 4,
                'password' => '$2y$12$KHls4k7vVjnV.izKhlDE9.bc/HjaaX0n/h.fEEHF25kiKeEiMRd5C',
                'remember_token' => null,
                'email_verified_at' => null,
            ],
            [
                'id_usuario' => 7,
                'nombre' => 'Clara',
                'apellidos' => 'Martínez Rubio',
                'email' => 'claramartinezr@bioscan.com',
                'id_rol' => 5,
                'password' => '$2y$12$tnsHUaxUK9xUhnUbi9DIYeMgVrDSIRkYrKvlv3mmkI6HQI496qAjS',
                'remember_token' => null,
                'email_verified_at' => null,
            ],
        ], ['id_usuario'], ['nombre', 'apellidos', 'email', 'id_rol', 'password', 'remember_token', 'email_verified_at']);
    }
}
