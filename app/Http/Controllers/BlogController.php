<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function show() {
        $posts = BlogPost::paginate(12);
        return view('blog', ['posts' => $posts]);
    }
}
