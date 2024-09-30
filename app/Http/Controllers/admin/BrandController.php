<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request){
        $brands = Brand::latest('id');

        if($request->get('keyword')){
            $brands = $brands->where('name', 'LIKE', '%'.$request->keyword.'%');
        }

        $brands = $brands->paginate(10);

        return view('admin.brands.list', compact('brands'));
    }
    
    public function create(){
        return view('admin.brands.create');
    }

    public  function store(Request $request){
       $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'slug' => 'required|unique:brands',
        ]);

        if($validator->passes()){
           $brand  = new Brand();
           $brand->name = $request->name;
           $brand->slug = $request->slug;
           $brand->status = $request->status;
           $brand->save();
           return response()->json([
               'status'=> true,
               'message'=> 'Brand created successfully',
               'brand'=> $brand,
           ]);
        }else{
            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors(),
            ]);
        }
    }

    public function edit($id, Request $request){
        $brand = Brand::find($id);
        if(empty($brand)){
            $request->session()->flash('error', 'No brand found');
            return redirect()->route('admin.brand.list');
        }

        $data['brand'] = $brand;

        return view('admin.brands.edit', $data);
    }

   public function update($id, Request $request){
    $brand = Brand::find($id);

    if (empty($brand)) {
        $request->session()->flash('error', 'No brand found');
        return response()->json([
            'status' => false,
            'notFound' => true
        ]);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,' . $brand->id,
    ]);

    if ($validator->passes()) {
        // Use the existing brand record, don't create a new one
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->save();

        return response()->json([
            'status' => true,
            'message' => 'Brand updated successfully',
        ]);
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ]);
    }
}

}
