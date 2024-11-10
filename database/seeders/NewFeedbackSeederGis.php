<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NewFeedbackSeederGis extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = file_get_contents(public_path('texts/florida_2gis_rev.json'));

        foreach (json_decode($reviews) as $item) {
            // if (!isset($item->author)) continue;

            $ing_lnk = $item->user->photo_preview_urls->url?$item->user->photo_preview_urls->url:"";
            if ($ing_lnk)
                Storage::disk('public')->put("gis2_fb_".$item->id.".webp", file_get_contents($ing_lnk), 'public');
            $data = [
                'name' => $item->user->name,
                'data' => date("Y-m-d", strtotime($item->date_created)),
                'platform' => "2gis",
                'img' => ($ing_lnk)?Storage::url("gis2_fb_".$item->id.".webp"):"",
                'score' => $item->rating,
                'description' => $item->text,
                'platform_lnk' => "https://2gis.ru/kursk/branches/70000001036185620"
            ];

            print_r($data);
            DB::table("new_feedback")->insert($data);
        }
    }
}
