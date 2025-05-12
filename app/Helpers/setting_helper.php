<?php

use App\Models\Setting;

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            $settings = Setting::first(); // atau bisa juga cache('setting')
        }

        return $settings->{$key} ?? $default;
    }
}
