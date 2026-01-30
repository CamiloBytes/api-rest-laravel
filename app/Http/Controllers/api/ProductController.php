<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

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
        $user = Auth::user();
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
            'avatar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $product = Product::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'sku' => $request->sku,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'status' => $request->status,
            'avatar' => $request->avatar,
        ]);

        if (!$product) {
            $data = [
                'message' => 'Error al crear el producto',
                'status' => 500
            ];
            return response()->json($data, 500);
        }


        $data = [
            'message' => 'Producto creado exitosamente',
            'data' => $product,
            'status' => 201
        ];
        return response()->json($data, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
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
            'avatar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'status' => $request->status,
            'avatar' => $request->avatar,
        ]);

        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'data' => $product,
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
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
            'products.*.avatar' => 'nullable|string',
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
                'avatar' => $productData['avatar'] ?? null,
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
