# 📦 Configuración de Base de Datos – Laravel 12 (Guía Rápida)

Este proyecto usa **Laravel 12** y puede conectarse tanto a **SQLite (local)** como a **MySQL (producción / Clever Cloud)**.

Esta guía explica **cómo conectarte correctamente a la base de datos**, **cuándo migrar** y **qué hacer si usas otro equipo**.

---

## 🧱 Requisitos

Antes de empezar, asegúrate de tener:

* PHP instalado
* Composer instalado
* Laravel funcionando (`php artisan serve`)

---

## 📁 Paso 1: Crear el archivo `.env`

⚠️ El archivo `.env` **NO se sube a Git**.

Cada persona / equipo debe crear el suyo.

Desde la raíz del proyecto:

```bash
cp .env.example .env
php artisan key:generate
```

Esto:

* Crea el archivo `.env`
* Genera la clave de la app (`APP_KEY`)

---

## 🛢️ Opción A: Usar MySQL (Recomendado – Clever Cloud)

### 1️⃣ Crear la base de datos

En Clever Cloud:

* Crear un **Add-on MySQL**
* Guardar los datos que entrega:

  * HOST
  * DATABASE
  * USER
  * PASSWORD
  * PORT

---

### 2️⃣ Configurar `.env`

Editar el `.env` y colocar:

```env
DB_CONNECTION=mysql
DB_HOST=tu-host-mysql
DB_PORT=3306
DB_DATABASE=nombre_db
DB_USERNAME=usuario_db
DB_PASSWORD=clave_db
```

⚠️ No usar `localhost`.

---

### 3️⃣ Limpiar caché de configuración

Siempre que se edite el `.env`:

```bash
php artisan config:clear
php artisan cache:clear
```

---

### 4️⃣ Migrar la base de datos

La primera vez que se conecta una base de datos nueva:

```bash
php artisan migrate
```

Esto crea las tablas definidas en las migraciones.

---

### 5️⃣ Ver estado de migraciones

```bash
php artisan migrate:status
```

---

## 🪶 Opción B: Usar SQLite (solo desarrollo / aprendizaje)

SQLite es válido para:

* Desarrollo local
* Pruebas
* Universidad

Configuración básica:

```env
DB_CONNECTION=sqlite
```

Y crear el archivo:

```bash
touch database/database.sqlite
```

⚠️ Para SQLite se recomienda:

```env
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

---

## 💻 ¿Qué pasa si me conecto desde otro equipo?

### 🔹 Caso 1: Base de datos remota (MySQL – Clever Cloud)

✅ Pasos:

1. Clonar el repositorio
2. Crear `.env`
3. Colocar las **mismas credenciales MySQL**
4. Limpiar caché

```bash
php artisan config:clear
```

❌ **NO es necesario migrar otra vez** si:

* La base ya tiene las tablas
* No hay migraciones nuevas

Laravel **NO vuelve a crear tablas que ya existen**.

---

### 🔹 Caso 2: Hay migraciones nuevas

Si otro desarrollador agregó migraciones:

```bash
php artisan migrate
```

Laravel solo ejecuta **las que no estén corridas**.

---

## ❓ ¿Existe un comando para “traer” la estructura de la DB?

❌ No.

Laravel funciona así:

* **Las migraciones son la fuente de verdad**
* La base de datos se construye a partir de ellas

Por eso:

* Las migraciones **sí se suben a Git**
* La base de datos **no**

---

## 📌 Buenas prácticas

* Usar nombres de tablas en **plural** (`products`)
* No subir `.env`
* Migrar solo cuando sea necesario
* Usar MySQL para producción
* SQLite solo para local

---

## ✅ Resumen rápido

✔ Cada equipo crea su `.env`
✔ MySQL remoto → no migrar si ya existe la DB
✔ Migrar solo si hay cambios
✔ Laravel maneja todo automáticamente

---

🧠 *Este README existe para que nadie se vuelva a enredar con la base de datos.*
