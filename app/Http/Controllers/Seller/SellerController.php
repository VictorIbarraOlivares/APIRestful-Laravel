<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class SellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendedores = Seller::has('products')->get();

        // return response()->json(['data' => $vendedores], 200);
        return $this->showAll($vendedores);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        // $vendedor = Seller::has('products')->findOrFail($id);

        return $this->showOne($seller);
    }
}
