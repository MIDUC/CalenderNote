<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'config_key' => 'app_name',
                'config_value' => 'MySchedule',
                'description' => 'Tên ứng dụng',
            ],
            [
                'config_key' => 'default_currency',
                'config_value' => 'VND',
                'description' => 'Đơn vị tiền tệ mặc định',
            ],
            [
                'config_key' => 'default_language',
                'config_value' => 'vi',
                'description' => 'Ngôn ngữ mặc định',
            ],
            [
                'config_key' => 'timezone',
                'config_value' => 'Asia/Ho_Chi_Minh',
                'description' => 'Múi giờ mặc định',
            ],
            [
                'config_key' => 'date_format',
                'config_value' => 'd/m/Y',
                'description' => 'Định dạng ngày tháng',
            ],
            [
                'config_key' => 'time_format',
                'config_value' => 'H:i',
                'description' => 'Định dạng giờ',
            ],
        ];

        foreach ($configs as $config) {
            \App\Models\Config::updateOrCreate(
                ['config_key' => $config['config_key']],
                $config
            );
        }
    }
}
