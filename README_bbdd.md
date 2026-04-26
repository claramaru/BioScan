# 🗄️ Base de Datos — BioScan2

Base de datos del proyecto **BioScan**, un sistema de gestión de animales en cebaderos que incluye control de alimentación, fichas médicas, gestión de usuarios y sistema de roles y privilegios.

- **Motor:** MariaDB 10.4.32
- **Charset:** utf8mb4 / utf8mb4_unicode_ci
- **Generado con:** phpMyAdmin 5.2.1

---

## 📋 Tablas del sistema

### 🐄 Núcleo de negocio

| Tabla | Descripción |
|-------|-------------|
| `cebadero` | Instalaciones donde se alojan los animales (nombre y ubicación) |
| `animal` | Registro de animales: especie, raza, lote, cebadero asignado y pienso recomendado |
| `pienso` | Catálogo de tipos de pienso disponibles (crecimiento, engorde, mantenimiento, adaptación) |
| `alimentacion` | Registro diario de alimentación por animal: tipo de pienso, cantidad y fecha |
| `ficha_medica` | Historial médico de cada animal: diagnóstico, tratamiento y observaciones |

### 👤 Usuarios y control de acceso

| Tabla | Descripción |
|-------|-------------|
| `usuario` | Usuarios de la aplicación con email, contraseña y rol asignado |
| `rol` | Roles del sistema: administrador, supervisor, operario, veterinario, invitado |
| `privilegio` | Permisos granulares sobre cada módulo (ver, crear, editar, borrar) |
| `rol_privilegio` | Relación N:M que asigna privilegios a cada rol |

### ⚙️ Infraestructura Laravel

| Tabla | Descripción |
|-------|-------------|
| `users` | Tabla de autenticación nativa de Laravel |
| `sessions` | Gestión de sesiones activas |
| `cache` / `cache_locks` | Sistema de caché de Laravel |
| `jobs` / `job_batches` / `failed_jobs` | Cola de trabajos en segundo plano |
| `migrations` | Historial de migraciones ejecutadas |
| `password_reset_tokens` | Tokens para recuperación de contraseña |

---

## 🔗 Relaciones principales

```
cebadero ──< animal >── pienso
animal ──< alimentacion >── pienso
animal ──< alimentacion >── usuario
animal ──< ficha_medica >── usuario
rol ──< rol_privilegio >── privilegio
usuario >── rol
```

---

## 👥 Roles y privilegios

| Rol | Privilegios |
|-----|-------------|
| `administrador` | Acceso total (todos los privilegios) |
| `supervisor` | Ver/crear/editar animales, alimentación y fichas; gestionar pienso |
| `operario` | Ver animales, ver/crear alimentación, ver fichas, editar observaciones |
| `veterinario` | Ver/editar animales, gestión completa de alimentación y fichas médicas |
| `invitado` | Solo lectura: ver animales, alimentación y fichas |

---

## 📄 Documentos

Aquí se almacenarán los documentos técnicos de la base de datos: esquemas, decisiones de diseño, scripts de migración, etc.

---

## 🧪 Tests

| Nombre | Enlace |
|--------|--------|
| Test de conexión | [Añadir enlace]() |
| Test de modelos / Eloquent | [Añadir enlace]() |
| Test de integridad referencial | [Añadir enlace]() |
| Test de privilegios por rol | [Añadir enlace]() |

---

## ☁️ Google Drive

Acceso a la carpeta de Drive con recursos de la base de datos (exportaciones, diagramas ER, backups de desarrollo):

🔗 [Carpeta Drive — Base de Datos](https://drive.google.com) ← *Reemplaza con el enlace real*

---

## 🖼️ Imágenes

La carpeta `/imagenes` contiene los recursos visuales utilizados en la documentación:

```
/imagenes
  ├── diagrama-er.png          # Diagrama entidad-relación completo
  ├── esquema-tablas.png       # Vista general de tablas y relaciones
  ├── roles-privilegios.png    # Mapa de roles y sus permisos
  └── ...
```

---

## 🤝 Contribución

Si modificas la base de datos (nuevas tablas, columnas, relaciones o datos de prueba), actualiza este README y añade la migración correspondiente en Laravel.
