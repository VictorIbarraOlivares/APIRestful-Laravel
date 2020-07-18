<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerSellerController extends ApiController
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
    public function index(Buyer $buyer)
    {
        $sellers = $buyer->transactions()
                         ->with('product.seller')
                         ->get()
                         ->pluck('product.seller')
                         ->unique('id')
                         ->values(); // reorganiza los indices, elimina los espacios vacios

        return $this->showAll($sellers);
    }

}
