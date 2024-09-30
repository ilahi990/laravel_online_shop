<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SubCategoryController extends Controller
{
    public function index(Request $request){
        $subCategory = SubCategory::select('sub_category.*', 'category.name as categoryName')
        ->latest('sub_category.id')
        ->leftJoin('category','category.id','sub_category.category_id');


        if(!empty($request->get('keyword'))){
            $subCategory  = $subCategory->where('sub_category.name', 'like', '%'.$request->get('keyword').'%');
            $subCategory  = $subCategory->orWhere('category.name', 'like', '%'.$request->get('keyword').'%');
        }
        

      $subCategory = $subCategory->paginate(10);

      return view('admin.sub_category.list', compact('subCategory'));
    }

    public function create(){
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        return view('admin.sub_category.create', $data);
    }

  public function store(Request $request){
    $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:sub_category',
        'category' =>'required',
        'status' => 'required'
    ]);

    if($validator->passes()){
        $subCategory = new SubCategory();
        $subCategory->name = $request->name;
        $subCategory->slug = $request->slug;
        $subCategory->status = $request->status;
        $subCategory->showHome = $request->showHome;
        $subCategory->category_id = $request->category;
        $subCategory->save();

        $request->session()->flash('success', 'Sub Category created successfully.');

        // Return a success response that includes the redirect URL
        return response()->json([
            'status' => true,
            'message' => 'Sub Category created successfully.',
            'redirect_url' => route('admin.sub-category.list') // Redirect to the sub-category list page
        ]);

    } else {
        // Handle validation errors
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}


public function edit($itemId, Request $request){
         $subCategory = SubCategory::find($itemId);

         if(empty($subCategory)){
             $request->session()->flash('error', 'Sub Category not found');
             return redirect()->route('admin.sub-category.list', compact('subCategory'));
         }

        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;
        $data['category_id'] = $subCategory->category_id; // Add this line to pass the category_id

         return view('admin.sub_category.edit', $data);
    }

public function update($itemId, Request $request)
{
    $subCategory = SubCategory::find($itemId);

    if (empty($subCategory)) {
        $request->session()->flash('error', 'Category not found');
        return response()->json([
            'status' => false,
            'notFound' => true,
            'message' => 'Category not found.'
        ]);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required|unique:category,slug,'.$subCategory->id.',id',
        'category' => 'required',
        'status' => 'required'
    ]);

    if ($validator->passes()) {
        $subCategory->name = $request->name;
        $subCategory->slug = $request->slug;
        $subCategory->status = $request->status;
        $subCategory->showHome = $request->showHome;
        $subCategory->category_id = $request->category;
        $subCategory->save();

        $request->session()->flash('success', 'Sub Category updated successfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category updated successfully',
            'redirect_url' => route('admin.sub-category.list') // Return redirect URL here
        ]);

    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}


 public function destroy($itemId, Request $request)
{
    $subCategory = SubCategory::find($itemId);
    
    if (empty($subCategory)) {
        return response()->json([
            'status' => false,
            'message' => 'Category not found.'
        ], 404);
    }

    // Delete the images
    // File::delete(public_path() . '/uploads/category/thumbs/' . $category->image);
    // File::delete(public_path() . '/uploads/category/' . $category->image);

    // Delete the category
    $subCategory->delete();

    return response()->json([
        'status' => true,
        'message' => 'Sub Category deleted successfully.'
    ]);
}

}
