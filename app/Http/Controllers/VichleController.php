<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VichleController extends Controller
{
    public function index(){
        $vichles = Vichle::orderBy('id', 'DESC')->paginate(request('limit') ?? 15);
        return returnPaginatedResourceData(VichleResource::collection($vichles));
    }

}
