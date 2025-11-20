<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
                'priority' => '1.0'
            ]);

            // Контакты
            $urls->push([
                'loc' => url('/contacts'),
                'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ]);

            // Доставка и оплата
            $urls->push([
                'loc' => url('/zones'),
                'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ]);

            $urls->push([
                'loc' => url('/reviews'),
                'lastmod' => Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ]);

            // Категории
            Category::all()->each(function ($item) use ($urls) {
                $urls->push([
                    'loc' => url("/catalog/{$item->slug}"),
                    'lastmod' => ($item->updated_at) ? $item->updated_at->format('Y-m-d\TH:i:s\Z') : Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                    'image' => $item->img ? url( $item->img) : null
                ]);
            });

            // Букеты
            Product::all()->each(function ($item) use ($urls) {
                $urls->push([
                    'loc' => url("/tovar/{$item->slug}"),
                    'lastmod' => ($item->updated_at) ? $item->updated_at->format('Y-m-d\TH:i:s\Z') : Carbon::yesterday()->format('Y-m-d\TH:i:s\Z'),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                    'image' => $item->img ? url( $item->img) : null
                ]);
            });



            return response()->view('sitemap.index', compact('urls'))
                ->header('Content-Type', 'text/xml');
        }
}
