<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\ProductImageController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('front.product');
Route::get('/cart', [CartController::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front.addToCart');

Route::group(['prefix' => 'admin'],function(){
    Route::group(['middleware' => 'admin.guest'], function(){
        
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });
    Route::group(['middleware' => 'admin.auth'], function(){
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
        
        // Route::get('/dashboard', function () {
            //     return view('admin.dashboard');
            // });
        });
    });

    // Dashboard routes
    Route::get('admin/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
    //  Category routes
    Route::get('admin/category', [CategoryController::class, 'index'])->name('admin.category.list');
    Route::get('admin/category/create', [CategoryController::class, 'create'])->name('admin.category.create');
    Route::post('admin/category', [CategoryController::class, 'store'])->name('admin.category.store');
    Route::get('admin/category/{item}/edit', [CategoryController::class, 'edit'])->name('admin.category.edit');
    Route::put('admin/category/{item}', [CategoryController::class, 'update'])->name('admin.category.update');
    Route::delete('admin/category/{item}/delete', [CategoryController::class, 'destroy'])->name('admin.category.delete');
    
    // Sub-Category routes
    Route::get('admin/sub-category', [SubCategoryController::class, 'index'])->name('admin.sub-category.list');
    Route::get('admin/sub-category/create',[SubCategoryController::class, 'create'])->name('admin.sub-category.create');
    Route::post('admin/sub-category/store',[SubCategoryController::class, 'store'])->name('admin.sub-category.store');
    Route::get('admin/sub-category/{item}/edit', [SubCategoryController::class, 'edit'])->name('admin.sub-category.edit');
    Route::put('admin/sub-category/{item}', [SubCategoryController::class, 'update'])->name('admin.sub-category.update');
    Route::delete('admin/sub-category/{item}/delete', [SubCategoryController::class, 'destroy'])->name('admin.sub-category.delete');

     
    //Brands routes
    Route::get('admin/brands', [BrandController::class, 'index'])->name('admin.brands.list');
    Route::get('admin/brands/create', [BrandController::class, 'create'])->name('admin.brands.create');
    Route::post('admin/brands', [BrandController::class,'store'])->name('admin.brands.store');
    Route::get('admin/brands/{brand}/edit', [BrandController::class, 'edit'])->name('admin.brands.edit');
    Route::put('admin/brands/{brand}', [BrandController::class, 'update'])->name('admin.brands.update');
    Route::delete('admin/brands/{brand}/delete', [BrandController::class, 'destroy'])->name('admin.brands.delete');
    
    //Product Routes
    Route::get('admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('admin/products/create',[ProductController::class, 'create'])->name('admin.products.create');
    Route::post('admin/products',[ProductController::class, 'store'])->name('admin.products.store');
    Route::get('admin/products/{product}/edit',[ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('admin/products/{product}',[ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('admin/products/{product}',[ProductController::class, 'destroy'])->name('admin.products.delete');
    Route::get('admin/get-products',[ProductController::class, 'getProducts'])->name('admin.products.getProducts');
    
    // product subcategory routes
    Route::get('admin/product-subcategories',[ProductSubCategoryController::class, 'index'])->name('admin.product-subcategories.index');

    Route::post('admin/product-images/update',[ProductImageController::class,'update'])->name('admin.product-images.update');
    Route::delete('admin/product-images',[ProductImageController::class,'destroy'])->name('admin.product-images.destroy');
    // Temp Images routes
    // Route::get('admin/category/temp-images', [TempImagesController::class, 'index'])->name('admin.category.

    Route::post('admin/upload-temp-image', [TempImagesController::class, 'create'])->name('admin.category.temp-images.create');
    Route::get('admin/category/getSlug', function(Request $request){
        $slug = '';
        if(!empty($request->title)){
            $slug = Str::slug($request->title);
        }
        return response()->json([
            'status' => true,
            'slug' => $slug
        ]);
    })->name('admin.category.getSlug');