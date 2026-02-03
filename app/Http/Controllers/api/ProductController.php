<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Service\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProductController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index()
    {
        $user = User::find(Auth::id());

        // Admin ve todos los productos, usuarios normales solo los suyos
        if ($user->isAdmin()) {
            $products = Product::paginate(10);
        } else {
            $products = Product::where('user_id', $user->id)->paginate(10);
        }

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'No products found'
            ], 200);
        }

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    public function show($id)
    {
        $user = User::find(Auth::id());
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Verificar acceso: admin o dueño del producto
        if (!$user->isAdmin() && $product->user_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permiso para ver este producto'
            ], 403);
        }

        return response()->json([
            'data' => $product
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $imageUrl = null;
        $imagePublicId = null;

        // Subir imagen si existe
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                $uploadResult = $this->cloudinaryService->upload(
                    $file,
                    'productos'
                );

                if (!$uploadResult['success']) {
                    return response()->json([
                        'message' => 'Error al subir la imagen',
                        'error' => $uploadResult['message'],
                        'status' => 500
                    ], 500);
                }

                $imageUrl = $uploadResult['url'];
                $imagePublicId = $uploadResult['public_id'];
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error al procesar la imagen',
                    'error' => $e->getMessage(),
                    'status' => 500
                ], 500);
            }
        }

        try {
            $product = Product::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'sku' => $request->sku,
                'category' => $request->category,
                'price' => $request->price,
                'stock' => $request->stock ?? 0,
                'status' => $request->status,
                'image' => $imageUrl,
                'image_public_id' => $imagePublicId,
            ]);

            return response()->json([
                'message' => 'Producto creado exitosamente',
                'data' => $product,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find(Auth::id());
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Verificar acceso: admin o dueño del producto
        if (!$user->isAdmin() && $product->user_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permiso para editar este producto'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        // Si hay nueva imagen, eliminar la anterior y subir la nueva
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($product->image_public_id) {
                $this->cloudinaryService->deleteImage($product->image_public_id);
            }

            // Subir nueva imagen
            $uploadResult = $this->cloudinaryService->upload(
                $request->file('image'),
                'productos'
            );

            if (!$uploadResult['success']) {
                return response()->json([
                    'message' => 'Error al subir la imagen',
                    'error' => $uploadResult['message'],
                    'status' => 500
                ], 500);
            }

            $product->image = $uploadResult['url'];
            $product->image_public_id = $uploadResult['public_id'];
        }

        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'data' => $product,
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find(Auth::id());
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Verificar acceso: admin o dueño del producto
        if (!$user->isAdmin() && $product->user_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este producto'
            ], 403);
        }

        // Eliminar imagen de Cloudinary si existe
        if ($product->image_public_id) {
            $this->cloudinaryService->deleteImage($product->image_public_id);
        }

        $product->delete();
        return response()->json([
            'message' => 'Producto eliminado exitosamente',
            'status' => 200
        ], 200);
    }

    /**
     * Insertar múltiples productos a la vez
     */
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
            'products.*.name' => 'required|string|max:255',
            'products.*.sku' => 'required|string|unique:products,sku',
            'products.*.category' => 'nullable|string|max:255',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.stock' => 'nullable|integer|min:0',
            'products.*.status' => 'nullable|string|max:255',
            'products.*.image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $createdProducts = [];
        $userId = Auth::id();

        foreach ($request->products as $productData) {
            $product = Product::create([
                'user_id' => $userId,
                'name' => $productData['name'],
                'sku' => $productData['sku'],
                'category' => $productData['category'] ?? null,
                'price' => $productData['price'],
                'stock' => $productData['stock'] ?? 0,
                'status' => $productData['status'] ?? null,
                'image' => $productData['image'] ?? null,
            ]);
            $createdProducts[] = $product;
        }

        return response()->json([
            'message' => 'Productos creados exitosamente',
            'total' => count($createdProducts),
            'data' => $createdProducts,
            'status' => 201
        ], 201);
    }
}
