<?php

use App\Models\SettingModel;

if (!function_exists('setting')) {
    /**
     * Helper global untuk mengambil data setting dinamis dari database.
     *
     * @param string $key
     * @param string|null $default
     * @return string
     */
    function setting(string $key, ?string $default = ''): string
    {
        static $settings = null;

        if ($settings === null) {
            $model = new SettingModel();
            $allSettings = $model->findAll();
            $settings = [];
            foreach ($allSettings as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }

        return $settings[$key] ?? $default;
    }
}