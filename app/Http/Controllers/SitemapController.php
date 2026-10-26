<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTag;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect();

        // Главные страницы
        $urls->push([
            'loc' => url('/'),
            'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);

        // Контакты
        $urls->push([
            'loc' => url('/contacts'),
            'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);

        // Доставка и оплата
        $urls->push([
            'loc' => url('/zones'),
            'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);

        $urls->push([
            'loc' => url('/reviews'),
            'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
            'changefreq' => 'weekly',
            'priority' => '0.9',
        ]);

        // Категории
        Category::all()->each(function ($item) use ($urls) {
            $urls->push([
                'loc' => url("/catalog/{$item->slug}"),
                'lastmod' => ($item->updated_at) ? $item->updated_at->format('Y-m-d\TH:i:s\Z') : Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'image' => $item->img ? url($item->img) : null,
            ]);
        });

        ProductTag::all()->each(function ($item) use ($urls) {
            $urls->push([
                'loc' => url("/tags/{$item->slug}"),
                'lastmod' => ($item->updated_at) ? $item->updated_at->format('Y-m-d\TH:i:s\Z') : Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'image' => $item->img ? url($item->img) : null,
            ]);
        });

        // Ценовые диапазоны
        collect(config('prices', []))->each(function ($range, $slug) use ($urls) {
            $urls->push([
                'loc' => url("/prices/{$slug}"),
                'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);
        });

        // Букеты
        Product::all()->each(function ($item) use ($urls) {
            $urls->push([
                'loc' => url("/tovar/{$item->slug}"),
                'lastmod' => ($item->updated_at) ? $item->updated_at->format('Y-m-d\TH:i:s\Z') : Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                'changefreq' => 'weekly',
                'priority' => '0.6',
                'image' => $item->img ? url($item->img) : null,
            ]);
        });

        // Статьи блога
        BlogPost::where('published_at', '<=', now())
            ->each(function ($item) use ($urls) {
                $urls->push([
                    'loc' => route('blog_page', $item->slug),
                    'lastmod' => ($item->updated_at ?? $item->published_at)?->format('Y-m-d\TH:i:s\Z')
                        ?? Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                    'image' => $item->img ? url($item->img) : null,
                ]);
            });

        return response()->view('sitemap.index', compact('urls'))
            ->header('Content-Type', 'text/xml');
    }
}
