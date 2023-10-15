<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vichle;
use App\Http\Resources\VichleResource;

class VichleController extends Controller
{
    public function index(){
        $vichles = Vichle::orderBy('id', 'DESC')->paginate(request('limit') ?? 15);
        return returnPaginatedResourceData(VichleResource::collection($vichles));
    }

}
