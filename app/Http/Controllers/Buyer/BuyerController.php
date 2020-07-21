<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Buyer;

class BuyerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:read-general')->only(['show']);
        $this->middleware('can:view,buyer')->only(['show']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->allowedAdminAction();
        $compradores = Buyer::has('transactions')->get();
        return $this->showAll($compradores);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Buyer $buyer)
    {
        // $comprador = Buyer::has('transactions')->findOrFail($id);
        return $this->showOne($buyer);
    }

}
