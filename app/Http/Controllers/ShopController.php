<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
{
    $categorySelected  = '';
    $subCategorySelected  = '';
    $brandsArray = [];

    if(!empty($request->get('brand'))){
        $brandsArray = explode(',', $request->get('brand'));
    }

    $categories = Category::orderBy('name','ASC')->with('sub_category')->where('status',1)->get();
    $brands     = Brand::orderBy('name','ASC')->where('status',1)->get();
    $products   = Product::where('status',1);

    // Apply category filter
    if(!empty($categorySlug)){
        $category = Category::where('slug', $categorySlug)->first();
        if($category) {
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }
    }

    // Apply subcategory filter
    if(!empty($subCategorySlug)){
        $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
        if($subCategory) {
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCategorySelected = $subCategory->id;
        }
    }
    // Apply brand filter
    if(!empty($brandsArray)){
        $products = $products->whereIn('brand_id', $brandsArray);
    }


    $products = $products->orderBy('id', 'DESC');
    $products =$products->paginate(10);

    $data['categories'] = $categories;
    $data['brands'] = $brands;
    $data['products'] = $products;
    $data['categorySelected'] = $categorySelected;
    $data['subCategorySelected'] = $subCategorySelected;
    $data['brandsArray'] = $brandsArray;

    return view('front.shop', $data);
}

public function product($slug){
    $product = Product::where('slug', $slug)->with('product_images')->first();
    if($product == null){
        abort(404);
    }

    $relatedProducts = [];
       //fetch related products
       if($product->related_products != ''){
         $productArray = explode(',',$product->related_products);
         $relatedProducts = Product::whereIn('id', $productArray)->get();
       }

    $data['product'] = $product;
    $data['relatedProducts'] = $relatedProducts;
    return view('front.product', $data);
}

}
