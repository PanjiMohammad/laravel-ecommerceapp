<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Jobs\ProductJob;
use App\Product;
use App\Category;
use App\Seller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::where('seller_id', auth()->guard('seller')->user()->id)->with(['category'])->orderBy('created_at', 'DESC');

        if (request()->q != '') {
            $product = $product->where('name', 'LIKE', '%' . request()->q . '%');
        }

        $product = $product->paginate(10);
        return view('seller.products.index', compact('product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //QUERY UNTUK MENGAMBIL SEMUA DATA CATEGORY
        $category = Category::orderBy('name', 'DESC')->get();
        return view('seller.products.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id', 
            'price' => 'required|integer',
            'weight' => 'required|integer',
            'image' => 'required|image|mimes:png,jpeg,jpg,webp' 
        ]);

        $seller = Auth::guard('seller')->user();

        //JIKA FILENYA ADA
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/products/');
            $file->move($destinationPath, $filename);

            // $destinationPath = public_path('products');
            // $file = $request->file('image');
            // $filename = time().Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            // $file->storeAs($destinationPath, $filename);

            $product = Product::create([
                'name' => $request->name,
                'slug' => $request->name,
                'category_id' => $request->category_id,
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'description' => $request->description,
                'image' => $filename, 
                'price' => $request->price,
                'weight' => $request->weight,
                'status' => $request->status
            ]);

            return redirect(route('product.newIndex'))->with(['success' => 'Produk Baru Ditambahkan']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id); 
        $category = Category::orderBy('name', 'DESC')->get(); 
        return view('seller.products.edit', compact('product', 'category')); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer',
            'weight' => 'required|integer',
            'image' => 'nullable|image|mimes:png,jpeg,jpg' //IMAGE BISA NULLABLE
        ]);

        $product = Product::find($id);
        $filename = $product->image;
        $seller = Auth::guard('seller')->user();
    
        //JIKA ADA FILE GAMBAR YANG DIKIRIM
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/products/');
            $file->move($destinationPath, $filename);

            File::delete($destinationPath . $product->image);
        }

        $product->update([
            'name' => $request->name,
            'slug' => $request->name,
            'description' => $request->description,
            'seller_id' => $seller->id,
            'seller_name' => $seller->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'weight' => $request->weight,
            'image' => $filename,
            'status' => $request->status
        ]);
        return redirect(route('product.newIndex'))->with(['success' => 'Data Produk Diperbaharui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id); 
        File::delete(storage_path('app/public/products/' . $product->image));
        $product->delete();
        return redirect(route('product.newIndex'))->with(['success' => 'Produk Sudah Dihapus']);
    }

    public function massUploadForm()
    {
        $category = Category::orderBy('name', 'DESC')->get();
        return view('products.bulk', compact('category'));
    }

    public function massUpload(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|mimes:xlsx' 
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '-product.' . $file->getClientOriginalExtension();
            $file->storeAs('public/uploads', $filename); 

            //BUAT JADWAL UNTUK PROSES FILE TERSEBUT DENGAN MENGGUNAKAN JOB
            //ADAPUN PADA DISPATCH KITA MENGIRIMKAN DUA PARAMETER SEBAGAI INFORMASI
            //YAKNI KATEGORI ID DAN NAMA FILENYA YANG SUDAH DISIMPAN
            ProductJob::dispatch($request->category_id, $filename);
            return redirect()->back()->with(['success' => 'Upload Produk Dijadwalkan']);
        }
    }
}
