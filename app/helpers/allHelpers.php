<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Фейковые аватары

if (!function_exists("load_fake_avatar_img")) {
    function load_fake_avatar_img():string {
        $name = "avatar_".rand(1, 5).".jpg";
        Storage::disk('public')->put($name, file_get_contents(public_path("faker_img/avatars/" . $name)), 'public');
        return Storage::url($name);
    }
}

if (!function_exists("value_check")) {
    function value_check($nameparam = null, $find = null, $default = null) {
        $value = Request::input($nameparam);
        if ($value == null)
            return $default;

        if (is_array($value)) {
            return in_array($find, $value);
        } else {
            return $value;
        }
    }
}
