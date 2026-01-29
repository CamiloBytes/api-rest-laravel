# 🚀 API REST Laravel con Autenticación Fortify y Sanctum

API REST desarrollada con Laravel 12, utilizando **Laravel Fortify** para la gestión de autenticación y **Laravel Sanctum** para la autenticación basada en tokens.

## 📋 Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Autenticación](#autenticación)
- [Endpoints de la API](#endpoints-de-la-api)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Seguridad](#seguridad)

---

## 📌 Requisitos

- PHP >= 8.2
- Composer
- SQLite / MySQL / PostgreSQL
- Node.js y NPM (opcional, para assets)

---

## ⚙️ Instalación

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>
cd api-rest-laravel-main
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar la base de datos
Edita el archivo `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=sqlite
# Para SQLite, asegúrate de que existe el archivo database/database.sqlite

# O para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tu_base_de_datos
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña
```

### 5. Ejecutar migraciones
```bash
php artisan migrate
```

### 6. Iniciar el servidor
```bash
php artisan serve
```

La API estará disponible en: `http://localhost:8000`

---

## 🔧 Configuración

### Laravel Fortify

Fortify está configurado para funcionar como API REST (sin vistas). La configuración se encuentra en:

- **`config/fortify.php`**: Configuración principal
  - `views` está deshabilitado (`false`)
  - `middleware` configurado como `['api']`
  
- **`app/Providers/FortifyServiceProvider.php`**: Proveedor de servicios
  - Autenticación personalizada
  - Rate limiting para login

- **`app/Actions/Fortify/CreateNewUser.php`**: Lógica de creación de usuarios

### Laravel Sanctum

Sanctum maneja la autenticación basada en tokens:

- **`config/sanctum.php`**: Configuración de tokens
- El modelo `User` incluye el trait `HasApiTokens`

---

## 📁 Estructura del Proyecto

```
app/
├── Actions/
│   └── Fortify/
│       ├── CreateNewUser.php          # Crear nuevos usuarios
│       ├── PasswordValidationRules.php # Reglas de validación de contraseña
│       ├── ResetUserPassword.php       # Resetear contraseña
│       ├── UpdateUserPassword.php      # Actualizar contraseña
│       └── UpdateUserProfileInformation.php
├── Http/
│   └── Controllers/
│       └── api/
│           ├── AuthController.php      # Controlador de autenticación
│           ├── UserControler.php       # Controlador de usuarios
│           └── ProductController.php   # Controlador de productos
├── Models/
│   ├── User.php                        # Modelo de usuario
│   └── Product.php                     # Modelo de producto
└── Providers/
    ├── AppServiceProvider.php
    └── FortifyServiceProvider.php      # Proveedor de Fortify

routes/
└── api.php                             # Definición de rutas API
```

---

## 🔐 Autenticación

### Sistema de Tokens (Sanctum)

La API utiliza tokens Bearer para autenticación. Después del registro o login, recibirás un token que debe incluirse en el header de todas las peticiones protegidas:

```
Authorization: Bearer <tu_token_aquí>
```

### Flujo de Autenticación

1. **Registro**: El usuario se registra y recibe un token
2. **Login**: El usuario inicia sesión y recibe un token
3. **Acceso**: Usar el token para acceder a rutas protegidas
4. **Logout**: Revocar el token actual
5. **Refresh**: Obtener un nuevo token (opcional)

---

## 📡 Endpoints de la API

### Base URL
```
http://localhost:8000/api
```

### Rutas Públicas (Sin autenticación)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/auth/register` | Registrar nuevo usuario |
| POST | `/api/auth/login` | Iniciar sesión |

### Rutas Protegidas (Requieren token)

#### Autenticación

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/auth/me` | Obtener usuario autenticado |
| POST | `/api/auth/logout` | Cerrar sesión (token actual) |
| POST | `/api/auth/logout-all` | Cerrar todas las sesiones |
| POST | `/api/auth/refresh` | Refrescar token |

#### Usuarios

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/users` | Listar todos los usuarios |
| GET | `/api/users/{id}` | Obtener un usuario específico |
| PUT | `/api/users/{id}` | Actualizar usuario completo |
| PATCH | `/api/users/{id}` | Actualizar usuario parcialmente |
| PUT | `/api/users/{id}/password` | Cambiar contraseña |
| DELETE | `/api/users/{id}` | Eliminar usuario |

#### Productos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/products` | Listar todos los productos |
| POST | `/api/products` | Crear nuevo producto |
| GET | `/api/products/{id}` | Obtener un producto específico |
| PUT | `/api/products/{id}` | Actualizar producto completo |
| PATCH | `/api/products/{id}` | Actualizar producto parcialmente |
| DELETE | `/api/products/{id}` | Eliminar producto |

---

## 💻 Ejemplos de Uso

### Registro de Usuario

**Request:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "username": "juan123",
    "email": "juan@ejemplo.com",
    "phone_number": "+34612345678",
    "password": "MiPassword123!",
    "password_confirmation": "MiPassword123!"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user": {
      "id": 1,
      "username": "juan123",
      "email": "juan@ejemplo.com",
      "phone_number": "+34612345678",
      "created_at": "2026-01-29T15:00:00.000000Z",
      "updated_at": "2026-01-29T15:00:00.000000Z"
    },
    "access_token": "1|abc123def456ghi789...",
    "token_type": "Bearer"
  }
}
```

### Iniciar Sesión

**Request:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "juan@ejemplo.com",
    "password": "MiPassword123!"
  }'
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Inicio de sesión exitoso",
  "data": {
    "user": {
      "id": 1,
      "username": "juan123",
      "email": "juan@ejemplo.com",
      "phone_number": "+34612345678"
    },
    "access_token": "2|xyz789abc123...",
    "token_type": "Bearer"
  }
}
```

### Obtener Usuario Autenticado

**Request:**
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer 2|xyz789abc123..." \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "juan123",
    "email": "juan@ejemplo.com",
    "phone_number": "+34612345678"
  }
}
```

### Actualizar Usuario

**Request:**
```bash
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer 2|xyz789abc123..." \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "username": "juan_actualizado",
    "email": "juan.nuevo@ejemplo.com",
    "phone_number": "+34698765432"
  }'
```

### Cambiar Contraseña

**Request:**
```bash
curl -X PUT http://localhost:8000/api/users/1/password \
  -H "Authorization: Bearer 2|xyz789abc123..." \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "current_password": "MiPassword123!",
    "password": "NuevaPassword456!",
    "password_confirmation": "NuevaPassword456!"
  }'
```

### Cerrar Sesión

**Request:**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer 2|xyz789abc123..." \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

---

## 🛡️ Seguridad

### Características de Seguridad Implementadas

1. **Autenticación por Token (Sanctum)**
   - Tokens seguros y revocables
   - Soporte para múltiples dispositivos
   - Tokens con prefijo configurable para detección en repositorios

2. **Rate Limiting**
   - Límite de 5 intentos por minuto en login
   - Protección contra ataques de fuerza bruta

3. **Validación de Contraseñas**
   - Mínimo 8 caracteres
   - Confirmación de contraseña requerida

4. **Protección de Datos**
   - Contraseñas hasheadas automáticamente
   - Campo `password` oculto en respuestas JSON

5. **Autorización**
   - Los usuarios solo pueden modificar/eliminar su propia cuenta
   - Middleware `auth:sanctum` en rutas protegidas

### Buenas Prácticas

- Siempre usa HTTPS en producción
- Guarda los tokens de forma segura (no en localStorage para SPAs críticas)
- Implementa refresh tokens para sesiones largas
- Configura CORS apropiadamente en `config/cors.php`
- Revisa y actualiza regularmente las dependencias

---

## 📝 Códigos de Estado HTTP

| Código | Significado |
|--------|-------------|
| 200 | OK - Petición exitosa |
| 201 | Created - Recurso creado exitosamente |
| 401 | Unauthorized - No autenticado o credenciales inválidas |
| 403 | Forbidden - No tiene permiso para esta acción |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Error de validación |
| 429 | Too Many Requests - Rate limit excedido |
| 500 | Internal Server Error - Error del servidor |

---

## 🧪 Testing

Para ejecutar los tests:

```bash
php artisan test
```

O con Pest:

```bash
./vendor/bin/pest
```

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

---

## 👨‍💻 Autor

Desarrollado con ❤️ usando Laravel
