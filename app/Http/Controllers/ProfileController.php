<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
  /**
   * Index of profile
   * 
   * Return the logged in user info
   * 
   */

  public function index(Request $request)
  {
    $user = $request->user();
    $id = $user->id;
    $name = $user->name;
    $email = $user->email;
    $updated_at = $user->updated_at;
    $purchases = count($user->transactions);
    $products = count($user->products);

    return $this->showOne([
      'id' => $id,
      'name' => $name,
      'email' => $email,
      'updated_at' => $updated_at,
      'purchases' => $purchases,
      'products' => $products,
    ]);
  }
}
