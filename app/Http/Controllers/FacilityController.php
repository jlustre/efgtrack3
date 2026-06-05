<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(): View
    {
        $facilities = Facility::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('facilities.index', [
            'facilities' => $facilities,
        ]);
    }
}
