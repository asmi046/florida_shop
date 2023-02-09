<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function show_page($slug) {
        $post = BlogPost::where('slug', $slug)->first();
        if($post == null) abort('404');

        return view('blog_page', ['post' => $post]);
    }

    public function show() {
        $posts = BlogPost::paginate(12);
        return view('blog', ['posts' => $posts]);
    }
}
