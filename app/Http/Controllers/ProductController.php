<?php

namespace App\Http\Controllers;

use Auth;
use App\Category;
use App\Product;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->role->id === Role::ROLE_STAFF) {
            $owner = ($user->role->id === Role::ROLE_STAFF) ? $user->owner : $user;
            $products =  Product::where('user_id','=', $owner->id)->get();
        } else if($user->role->id === Role::ROLE_USER) {
            $products = Product::all();
        }


        return view('product.index',['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('product.create',['categories' => $this->getCategories()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Request\CreateProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        $product = new Product;
        $product->user_id = Auth::id();
        $this->saveProduct($product, $request->all());
        return redirect(route('product.create'))->with('status', 'Product has been added ! Add more products');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        return view('product.show',['product' => $product]);
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
        $this->authorize('update', $product);
        return view('product.edit',['product' => $product, 'categories' => $this->getCategories()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Request\CreateProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateProductRequest $request, $id)
    {
        $product = Product::find($id);
        $this->authorize('update',$product);
        $this->saveProduct($product, $request->all());

        return redirect(route('product.index'))->with('status', 'Product has been modified !');
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
        $product->delete();

        return redirect(route('product.index'))->with('status', 'Product has been removed !');
    }

    /**
     * Get all categories
     * @return collect App\Category
     */
    private function getCategories()
    {
        return Category::select('id','name')
        ->orderBy('name','asc')
        ->get();
    }

    /**
     * Save product details
     *
     * @param App\Product $product
     * @param array $data
     * @return boolean
     */
    private function saveProduct(Product $product, $data)
    {
        $product->category_id = $data['category_id'];
        $product->name = $data['name'];
        $product->description = $data['description'];
        $product->price = $data['price'];

        return $product->save();
    }
}
