<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OPDLabController extends Controller
{
    public function index()
    {
        return view('opdLab'); // main OPD LAB page
    }

    public function secondScreen()
    {
        return view('opdLabSecondScreen'); // separate Blade for second screen
    }
}
