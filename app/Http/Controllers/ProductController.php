<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Transaction;

class ProductController extends Controller
{
  /**
   * Index of products
   * 
   * Return list of products in stock
   * 
   */

  public function index(Request $request)
  {
    return $this->showOne(
      Product::inStock()
        ->name($request->name)
        ->get()
    );
  }

  /**
   * Store products
   * 
   * Store a single product
   */
  public function store(ProductRequest $request)
  {
    $user_id = $request->user()->id;
    $product = new Product($request->all());
    $product->user_id = $user_id;
    if ($product->save()) {
      return $this->showOne([
        'message' => 'Registro del producto exitoso'
      ]);
    }
    return $this->showError('Error al guardar el producto', [], 400);
  }

  /**
   * Buy products
   * 
   * Buy a single product
   */
  public function buy(Request $request)
  {

    $productId = $request->id;
    $quantity = $request->quantity;
    $userId = $request->user()->id;

    // Validate the quantity field is not empty
    if (!$quantity) {
      return $this->showError('La cantidad de elementos a comprar es obligatoria', [], 400);
    }

    // No negative numbers
    if ($quantity < 1) {
      return $this->showError('La cantidad de elementos debe ser como múnimo 1', [], 400);
    }

    $product = Product::find($productId);
    if (!$product) {
      return $this->showError('El producto que desea comprar no existe en los registros', [], 404);
    }

    if ($quantity > $product->quantity) {
      return $this->showError('El producto no cuenta con suficientes elementos en el stock', [], 400);
    }

    // Purchase can be made

    $product->quantity = $product->quantity - $quantity;
    $product->save();

    $transaction = new Transaction([
      'quantity' => $quantity,
      'user_id' => $userId,
      'product_id' => $product->id,
    ]);

    $transaction->save();

    return $this->showOne([
      'message' => 'Su compra se ha realizado exitosamente',
      'transaction' => $transaction,
    ]);
  }
}
