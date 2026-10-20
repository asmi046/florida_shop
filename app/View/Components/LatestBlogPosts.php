<?php

namespace App\View\Components;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class LatestBlogPosts extends Component
{
    public Collection $posts;

    public function __construct()
    {
        $this->posts = BlogPost::query()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
    }

    public function render(): View
    {
        return view('components.latest-blog-posts');
    }
}
