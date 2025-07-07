<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    //

    public function index(){
        return view("front.index");
    }

    public function pricing(){
        return view("front.pricing");
    }

        public function how(){
        return view("front.how");
    }

        public function faq(){
        return view("front.faq");
    }
}
