<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Get all settings for a group
     *
     * @param string $group
     * @return array
     */
    function settings(string $group): array
    {
        return Setting::getByGroup($group);
    }
}
