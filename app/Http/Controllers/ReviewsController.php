<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Review;

class ReviewsController extends Controller
{
    public function show() {
        $rew = Review::paginate(15);
        return view('reviews', ["reviews" => $rew]);
    }
}
