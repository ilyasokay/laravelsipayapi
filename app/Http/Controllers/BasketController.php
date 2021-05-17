<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class BasketController extends Controller
{
    public function index()
    {
        if(!Cookie::get('shopping_cart')){
            return redirect()->route('index');
        }
        $cookie_data = stripslashes(Cookie::get('shopping_cart'));
        $cart_data = json_decode($cookie_data, true);

        return view('basket.index')
            ->with('cart_items', $cart_data);
    }

    public function basketAdd(Request $request, $product_id)
    {
        $prod_id = $product_id;
        $quantity = $request->input('quantity');

        if (Cookie::get('shopping_cart')) {
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
            $cart_data = json_decode($cookie_data, true);
        } else {
            $cart_data = array();
        }

        $item_id_list = array_column($cart_data, 'item_id');
        $prod_id_is_there = $prod_id;

        if (in_array($prod_id_is_there, $item_id_list)) {
            foreach ($cart_data as $keys => $values) {
                if ($cart_data[$keys]["item_id"] == $prod_id) {
                    if($request->input('type') == "update"){
                        $cart_data[$keys]["item_quantity"] = $request->input('quantity');
                    }else{
                        $cart_data[$keys]["item_quantity"] += $request->input('quantity');
                    }

                    $item_data = json_encode($cart_data);
                    $minutes = 60;
                    Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));

                    return response()->json([
                        'message' => 'Adding to cart is successful.',
                        'basket_count' => count($cart_data)
                    ]);

                    return response()->json(['status' => '"' . $cart_data[$keys]["item_name"] . '" Already Added to Cart', 'status2' => '2']);
                }
            }
        } else {
            $products = Product::find($prod_id);
            $prod_name = $products->name;
            $prod_image = $products->image_url;
            $priceval = $products->price;

            if ($products) {
                $item_array = array(
                    'item_id' => $prod_id,
                    'item_name' => $prod_name,
                    'item_quantity' => $quantity,
                    'item_price' => $priceval,
                    'item_image' => $prod_image
                );
                $cart_data[] = $item_array;

                $item_data = json_encode($cart_data);
                $minutes = 60;
                Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));

                return response()->json([
                    'message' => 'Adding to cart is successful.',
                    'basket_count' => count($cart_data)
                ]);
            }
        }
    }

    public function basketItemRemove(Request $request, $id)
    {
        $prod_id = $id;

        $cookie_data = stripslashes(Cookie::get('shopping_cart'));
        $cart_data = json_decode($cookie_data, true);

        $item_id_list = array_column($cart_data, 'item_id');
        $prod_id_is_there = $prod_id;

        if(in_array($prod_id_is_there, $item_id_list))
        {
            foreach($cart_data as $keys => $values)
            {
                if($cart_data[$keys]["item_id"] == $prod_id)
                {
                    unset($cart_data[$keys]);
                    $item_data = json_encode($cart_data);
                    $minutes = 60;
                    Cookie::queue(Cookie::make('shopping_cart', $item_data, $minutes));
                    //return response()->json(['status'=>'Item Removed from Cart']);
                }
            }
        }

        if(count($cart_data) > 0){
            return redirect()->route('basket.index');
        }

        return redirect()->route('index');
    }


    public function basketCount()
    {
        $cart_data = [];

        if (Cookie::get('shopping_cart')) {
            $cookie_data = stripslashes(Cookie::get('shopping_cart'));
            $cart_data = json_decode($cookie_data, true);
        }

        return response()->json([
            'basket_count' => count($cart_data)
        ]);
    }

}
