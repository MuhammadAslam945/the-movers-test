<?php

namespace App\Http\Controllers;

use App\Models\Request\Request as RequestRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index($id)
    {
        
        try{
            $requests = RequestRequest::where('user_id',$id)->count();
            return $requests;
        }catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
}
