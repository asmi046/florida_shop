<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Celebration;

class CelebrationController extends Controller
{
    public function index($slug) {
        $categoryInfo = Celebration::where('slug', $slug)->first();

        if($categoryInfo == null) abort('404');

        $allproduct = $categoryInfo->celebration_tovars;

        return view("celebrations", ['cat_info' => $categoryInfo, 'allproduct' => $allproduct]);
    }
}
