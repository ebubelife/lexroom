<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key'   => 'default_currency',
                'value' => 'gbp',
                'label' => 'Default Currency',
                'group' => 'payments',
            ],
            [
                'key'   => 'platform_name',
                'value' => 'FirstMediator',
                'label' => 'Platform Name',
                'group' => 'general',
            ],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
