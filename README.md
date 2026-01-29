# Api rest con laravel

### Comandos basicos que se debe utilizar

```bash
php artisan install:api
```

dentro de Routes/api.php se crea el crud completo, las rutas del mismo asi:

```bash
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ruta api para listar productos
Route::get('/products', function () {
    return 'Products List';
});

//traer un producto en especifico
Route::get('/products/{id}', function () {
    return "Product Details";
});

//crear
Route::post('/products', function () {
    return 'Create Product';
});

//editar
Route::put('/products/{id}', function () {
    return 'Update Products';
});


//eliminar
Route::delete('/products/{id}', function () {
    return 'Delete Product';
});
```

luego se usa una extencioncomo flashpost, thunder client o el mismo postman para testear la api.

se coloca la siguinete ruta: http://127.0.0.1:8000/api/products

y se hacen las peticiones correspondientes.

---

# consulta con db real

se ejecuta el comando

```bash
php artisan make:migration create_products_table
```

> el nombre de la tabla depedne del crud

esto comando crea la migracion en la carpeta database/migrations

se crea lo siguiente:

```bash
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
```

luego la editas a tu necesidad

```bash
public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }
```

luego se migra eso para crear las tablas en la base de datos con:

```bash
php artisan migrate
```

y se crearn las tablas en la base de datos.

luego se crea un modelo como:

```bash
php artisan make:model Product
```

esto crea un Products dentro de la carpeta model y se ajusta segun a tu crud, ejemplo:

```bash
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'product';

    // se crea este fillabe para ver que campos pueden ser alterdos
    protected $fillable = [
        'name',
        'description',
        'price',
    ];
}
```

luego se crea un controlador, para llamar los endpoinr de api.php desde ahi y sea mde manera mas simple y limpia.

```bash
php artisan make:controller ProductController
```

y te crea uno por defecto.

luego en route/api.php se arrglan los endoipts usando el controlador de esta manera:

```bash
use App\Http\Controllers\ProductController; // se importa el controlador de productos

// ruta api para listar productos
Route::get('/products', [ProductController::class, 'index']);
```
