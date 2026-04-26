# BioScan

Aplicación web para la gestión de explotaciones ganaderas, animales, alimentación y usuarios.

## Objetivo

BioScan permite registrar y consultar información relacionada con:
- animales,
- cebaderos,
- alimentación,
- pienso,
- fichas médicas,
- usuarios y roles.

## Stack tecnológico

- PHP 8.2
- Laravel 12
- Blade
- JavaScript
- Vite
- Bootstrap 5
- Bootstrap Icons
- CSS personalizado
- MySQL / MariaDB

## Estructura del proyecto

- `app/` → Lógica principal de la aplicación (modelos, controladores, providers)
- `resources/views/` → Vistas Blade
- `resources/js/` → JavaScript fuente
- `resources/css/` → CSS fuente
- `public/` → Archivos públicos compilados y recursos estáticos
- `routes/` → Definición de rutas web y API
- `database/migrations/` → Migraciones de base de datos
- `database/seeders/` → Datos de ejemplo o iniciales
- `tests/` → Tests automáticos

## Módulos principales

- `Animales` → Alta, edición, consulta e historial de animales
- `Cebaderos` → Gestión de cebaderos
- `Alimentación` → Registro y consulta de alimentación
- `Piensos` → Gestión de tipos de pienso
- `Usuarios y roles` → Control de acceso y permisos
- `Ficha médica` → Información sanitaria asociada a animales

## Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL o MariaDB
