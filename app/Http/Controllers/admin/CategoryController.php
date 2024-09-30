<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use  App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Image;

class CategoryController extends Controller
{
    public function index(Request $request){
        $category = Category::latest();

        if(!empty($request->get('keyword'))){
            $category  = $category->where('name', 'like', '%'.$request->get('keyword').'%');
        }
        

      $category = $category->paginate(10);

      return view('admin.category.list', compact('category'));
    }

    public function create(){
        return view('admin.category.create');
    }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required|unique:category', // Change 'categories' to 'category'
    ]);

    if ($validator->passes()) {
        try {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->image = null;
            $category->save();

            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath, $dPath );

                 // Generate Image Thumbnail
                $dPath = public_path().'/uploads/category/thumbs/'.$newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint){
                   $constraint->upsize();
                });
                $img->save($dPath);

                    $category->image = $newImageName;
                    $category->save();
                
            }

            $request->session()->flash('success', 'Category added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category created successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please check logs.'
            ], 500);
        }
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}



    public function edit($itemId, Request $request)
    {
         $category = Category::find($itemId);

         if(empty($category)){
             return redirect()->route('admin.category.list', compact('category'));
         }
         return view('admin.category.edit', compact('category'));
    }

   public function update($itemId, Request $request)
{
    $category = Category::find($itemId);

    if (empty($category)) {
        $request->session()->flash('error', 'Category not found');
        return response()->json([
            'status' => false,
            'notFound' => true,
            'message' => 'Category not found.'
        ]);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        // 'slug' => 'required|unique:category,slug,'.$category->id .',id',
    ]);

    if ($validator->passes()) {
        $category->name = $request->name;
        $category->slug = $request->slug;
        // $category->image = $request->image;
        $category->status = $request->status;
        $category->showHome = $request->showHome;

        // Handle image upload
        $oldImage = $category->image;
        if (!empty($request->image_id)) {
            $tempImage = TempImage::find($request->image_id);
            $extArray = explode('.', $tempImage->name);
            $ext = end($extArray);

            $newImageName = $category->id . '-' . time() . '.' . $ext;
            $sPath = public_path() . '/temp/' . $tempImage->name;
            $dPath = public_path() . '/uploads/category/' . $newImageName;

            File::copy($sPath, $dPath);

            // Generate Image Thumbnail
            $thumbPath = public_path() . '/uploads/category/thumbs/' . $newImageName;
            $img = Image::make($sPath);
            $img->fit(450, 600, function ($constraint) {
                $constraint->upsize();
            });
            $img->save($thumbPath);

            $category->image = $newImageName;
            $category->save();

            // Delete old images
            File::delete(public_path() . '/uploads/category/thumbs/' . $oldImage);
            File::delete(public_path() . '/uploads/category/' . $oldImage);
        }

        $request->session()->flash('success', 'Category updated successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully'
        ]);
    } else {
        \Log::error($validator->errors()); // Log errors for debugging
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}


 public function destroy($itemId, Request $request)
{
    $category = Category::find($itemId);
    
    if (empty($category)) {
        return response()->json([
            'status' => false,
            'message' => 'Category not found.'
        ], 404);
    }

    // Delete the images
    File::delete(public_path() . '/uploads/category/thumbs/' . $category->image);
    File::delete(public_path() . '/uploads/category/' . $category->image);

    // Delete the category
    $category->delete();

    return response()->json([
        'status' => true,
        'message' => 'Category deleted successfully.'
    ]);
}

}