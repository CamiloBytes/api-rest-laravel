# 📚 Comandos Esenciales de Laravel

## 🚀 Instalación y Configuración Inicial

### Crear un nuevo proyecto Laravel
```bash
composer create-project laravel/laravel nombre-proyecto
```

### Instalar API (Sanctum)
```bash
php artisan install:api
```

### Instalar dependencias
```bash
composer install
npm install
```

### Generar APP_KEY
```bash
php artisan key:generate
```

### Configurar permisos (Linux)
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

---

## 🗄️ Base de Datos

### Migraciones

```bash
# Crear migración
php artisan make:migration create_products_table

# Crear migración para modificar tabla
php artisan make:migration add_role_to_users_table --table=users

# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Revertir todas las migraciones
php artisan migrate:reset

# Refrescar base de datos (elimina y recrea)
php artisan migrate:fresh

# Refrescar y ejecutar seeders
php artisan migrate:fresh --seed
```

### Seeders

```bash
# Crear seeder
php artisan make:seeder ProductSeeder

# Ejecutar todos los seeders
php artisan db:seed

# Ejecutar un seeder específico
php artisan db:seed --class=ProductSeeder
```

### Factories

```bash
# Crear factory
php artisan make:factory ProductFactory
```

---

## 🎨 Modelos

```bash
# Crear modelo
php artisan make:model Product

# Crear modelo con migración
php artisan make:model Product -m

# Crear modelo con migración, factory y seeder
php artisan make:model Product -mfs

# Crear modelo con todo (migración, factory, seeder, controller)
php artisan make:model Product -a

# Listar todos los modelos
php artisan model:show
```

---

## 🎮 Controladores

```bash
# Crear controlador básico
php artisan make:controller ProductController

# Crear controlador de recursos (con métodos CRUD)
php artisan make:controller ProductController --resource

# Crear controlador API (sin create y edit)
php artisan make:controller ProductController --api

# Crear controlador dentro de carpeta
php artisan make:controller api/ProductController
```

---

## 🛣️ Rutas

```bash
# Listar todas las rutas
php artisan route:list

# Listar rutas específicas
php artisan route:list --path=api

# Limpiar caché de rutas
php artisan route:clear

# Cachear rutas (producción)
php artisan route:cache
```

---

## 🔐 Autenticación y Seguridad

```bash
# Crear middleware
php artisan make:middleware AdminMiddleware

# Crear policy
php artisan make:policy ProductPolicy

# Crear request de validación
php artisan make:request StoreProductRequest
```

---

## 🧪 Testing

```bash
# Crear test
php artisan make:test ProductTest

# Crear test unitario
php artisan make:test ProductTest --unit

# Ejecutar tests
php artisan test

# Ejecutar tests con cobertura
php artisan test --coverage
```

---

## 🧹 Limpieza y Caché

```bash
# Limpiar todas las cachés
php artisan optimize:clear

# Limpiar caché de aplicación
php artisan cache:clear

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de vistas
php artisan view:clear

# Limpiar logs
rm -rf storage/logs/*.log

# Cachear todo (producción)
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔧 Artisan

```bash
# Ver todos los comandos disponibles
php artisan list

# Crear comando personalizado
php artisan make:command SendEmails

# Ejecutar comando personalizado
php artisan app:send-emails
```

---

## 📦 Composer

```bash
# Instalar paquete
composer require nombre/paquete

# Actualizar dependencias
composer update

# Autoload clases
composer dump-autoload

# Ver paquetes instalados
composer show
```

---

## 🖥️ Servidor

```bash
# Iniciar servidor de desarrollo
php artisan serve

# Iniciar en puerto específico
php artisan serve --port=8080

# Iniciar en host específico
php artisan serve --host=0.0.0.0
```

---

## 📝 Comandos Útiles

```bash
# Ver información de Laravel
php artisan about

# Modo mantenimiento (activar)
php artisan down

# Modo mantenimiento (desactivar)
php artisan up

# Inspeccionar modelo
php artisan model:show Product

# Crear enlace simbólico para storage
php artisan storage:link

# Crear evento
php artisan make:event ProductCreated

# Crear listener
php artisan make:listener SendProductNotification

# Crear job
php artisan make:job ProcessOrder

# Crear mail
php artisan make:mail OrderShipped

# Crear notificación
php artisan make:notification InvoicePaid
```

---

## 🐛 Depuración

```bash
# Activar modo debug
# En .env: APP_DEBUG=true

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Tinker (consola interactiva)
php artisan tinker
```

---

## 📊 Queue (Colas)

```bash
# Crear tabla de trabajos
php artisan queue:table
php artisan migrate

# Ejecutar worker
php artisan queue:work

# Ejecutar un trabajo específico
php artisan queue:work --queue=emails

# Ver trabajos fallidos
php artisan queue:failed

# Reintentar trabajos fallidos
php artisan queue:retry all
```

---

## 🔄 Versionado y Git

```bash
# Crear .gitignore (ya viene por defecto en Laravel)

# Archivos importantes a NO versionar:
# - .env
# - /vendor
# - /node_modules
# - /storage/*.key
# - /storage/logs/*
```

---

## 🌐 API y Sanctum

```bash
# Publicar configuración de Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Ejecutar migraciones de Sanctum
php artisan migrate
```

---

## 💡 Tips de Productividad

```bash
# Generar un modelo completo con todo
php artisan make:model Product -a

# Esto crea:
# - Model (Product)
# - Migration (create_products_table)
# - Factory (ProductFactory)
# - Seeder (ProductSeeder)
# - Controller (ProductController)
# - Policy (ProductPolicy)
```

---

## 📌 Comandos más usados en el día a día

```bash
# 1. Crear modelo con migración
php artisan make:model Product -m

# 2. Crear controlador
php artisan make:controller ProductController

# 3. Migrar base de datos
php artisan migrate

# 4. Listar rutas
php artisan route:list

# 5. Limpiar caché
php artisan optimize:clear

# 6. Iniciar servidor
php artisan serve
```

---

## 🆘 Solución de Problemas Comunes

```bash
# Error de permisos en storage
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache

# Error con composer
composer clear-cache
composer install

# Error con .env
cp .env.example .env
php artisan key:generate

# Base de datos bloqueada (SQLite)
php artisan migrate:fresh

# Clase no encontrada
composer dump-autoload
```

---

## 📱 Frontend (Opcional)

```bash
# Compilar assets
npm run dev

# Compilar para producción
npm run build

# Watch mode
npm run watch
```

---

**📝 Nota:** Estos comandos están actualizados para Laravel 11.x

**🔗 Documentación oficial:** https://laravel.com/docs
