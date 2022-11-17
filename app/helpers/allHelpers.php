<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Фейковые аватары

if (!function_exists("load_fake_avatar_img")) { 
    function load_fake_avatar_img():string {
        $name = "avatar_".rand(1, 5).".jpg";
        Storage::disk('local')->put("public/rewev_avatars/".$name, file_get_contents(public_path("faker_img\avatars\\" . $name)), 'public');
        return $name;
    }
}