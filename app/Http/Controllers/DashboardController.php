<?php

namespace App\Http\Controllers;

use App\Models\Clinic;

class DashboardController extends Controller
{
    public function index()
    {
        // $clinics = Clinic::all();
        $clinics = Clinic::has('queues')->get();
        return view('dashboard', compact('clinics'));
    }
}
