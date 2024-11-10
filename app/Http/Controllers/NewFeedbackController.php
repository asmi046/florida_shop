<?php

namespace App\Http\Controllers;

use App\Models\NewFeedback;
use Illuminate\Http\Request;

class NewFeedbackController extends Controller
{
    public function index() {
        $paltforms = NewFeedback::select('platform')->groupBy('platform')->get();
        $all_reviews = NewFeedback::all();

        $sorted_review = [];

        foreach ($all_reviews as $item) {
            $sorted_review[$item->platform][] = $item;
            $sorted_review['all'][] = $item;
        }

        return [
            'platforms' => $paltforms,
            'reviews' => $sorted_review
        ];
    }
}
