<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{

    public function index(Request $request){
        $brands = Brand::latest('id');
        if($request->get('keyword')){
            $brand = $brands->where('name','like','%'.$request->keyword.'%');
        }
        $brands = $brands->paginate(10);

        return view('admin.brand.list',compact('brands'));
    }


    public function create(){
        return view('admin.brand.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands'
        ]);

        if($validator->passes()){
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edit($id,Request $request){
        $brand = Brand::find($id);
        if(empty($brand)){
            session()->flash('error','Record not found');
            return redirect()->route('brands.index');
        }

        $data['brand'] = $brand;
        return view('admin.brand.edit',$data);

    }

    public function update($id,Request $request){
        $brand = Brand::find($id);

        if(empty($brand)){
            session()->flash('error','Record not found');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);

        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
        ]);

        if($validator->passes()){
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function destroy($id,Request $request){
        $brand = Brand::find($id);

        if(empty($brand)){
            session()->flash('error','Record not found');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }

        $brand->delete();

        session()->flash('success','Brand deleted successfully.');

        return response([
            'status' => true,
            'message' => 'Brand deleted successfully.'
        ]);

    }

}
