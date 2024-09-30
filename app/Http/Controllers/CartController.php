<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $product = Product::with('product_images')->find($request->id);
        
        if($product == null){
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if(Cart::count() > 0){
           // Products found in cart
           // Check if this product already in the cart
           // Return as message that product already added in your cart
           // if product not in the cart, then add product in cart

           $cartContent = Cart::content();
           $productAlreadyExist = false;

           foreach ($cartContent as $item){
            if($item->id == $product->id){
                $productAlreadyExist = true;
                // break;
            }
           }

           if($productAlreadyExist == false){
            Cart::add($product->id, $product->title, 1, $product->price, ['productImages' => (!empty($product->product_images)) ? $product->product_images->first() : '' ]);
           }else{
            return response()->json([
               'status' => false,
               'message' => 'Product already added in your cart'
            ]);
           }
        }else {

            Cart::add($product->id, $product->title, 1, $product->price, ['productImages' => (!empty($product->product_images)) ? $product->product_images->first() : '' ]);
        }

        return response()->json([
           'status' => true,
           'message' => 'Product added to cart successfully',
            // 'cart' => Cart::content()
        ]);
    }

    public function cart(){
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }
}
