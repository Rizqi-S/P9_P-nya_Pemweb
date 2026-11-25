<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles CRUD operations and reset actions for school supply products.
 */
class ProductController extends Controller
{
    /**
     * Display the main bubble chart and product management page.
     */
    public function index(): View
    {
        $products = $this->orderedProducts();

        return view('products.index', compact('products'));
    }

    /**
     * Store a newly created product in the database.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validateProduct($request);
        Product::create($validated);

        return $this->respond($request, 'Produk berhasil ditambahkan.', 201);
    }

    /**
     * Update the specified product in the database.
     */
    public function update(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $product->update($validated);

        return $this->respond($request, 'Data produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from the database.
     */
    public function destroy(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $product->delete();

        return $this->respond($request, 'Produk berhasil dihapus.');
    }

    /**
     * Truncate the products table and re-seed the 30 initial products.
     */
    public function reset(Request $request, ProductSeeder $seeder): JsonResponse|RedirectResponse
    {
        Product::truncate();
        $seeder->run();

        return $this->respond($request, 'Data awal berhasil dikembalikan.');
    }

    /**
     * Validate product input fields from the request.
     *
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'produk' => ['required', 'string', 'max:80'],
            'harga' => ['required', 'integer', 'min:0'],
            'keuntungan' => ['required', 'integer', 'min:0'],
            'terjual' => ['required', 'integer', 'min:0'],
        ], [
            'produk.required' => 'Nama produk wajib diisi.',
            'produk.max' => 'Nama produk maksimal 80 karakter.',
            'harga.required' => 'Harga produk wajib diisi.',
            'harga.integer' => 'Harga produk harus berupa bilangan bulat.',
            'harga.min' => 'Harga produk harus bernilai 0 atau lebih.',
            'keuntungan.required' => 'Keuntungan wajib diisi.',
            'keuntungan.integer' => 'Keuntungan harus berupa bilangan bulat.',
            'keuntungan.min' => 'Keuntungan harus bernilai 0 atau lebih.',
            'terjual.required' => 'Jumlah terjual wajib diisi.',
            'terjual.integer' => 'Jumlah terjual harus berupa bilangan bulat.',
            'terjual.min' => 'Jumlah terjual harus bernilai 0 atau lebih.',
        ]);
    }

    /**
     * Retrieve all products ordered consistently by primary key.
     */
    private function orderedProducts()
    {
        return Product::orderBy('id')->get(['id', 'produk', 'harga', 'keuntungan', 'terjual']);
    }

    /**
     * Return a JSON payload for AJAX calls or a redirect for standard form posts.
     */
    private function respond(Request $request, string $message, int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'products' => $this->orderedProducts(),
            ], $status);
        }

        return redirect()->route('products.index')->with('success', $message);
    }
}
