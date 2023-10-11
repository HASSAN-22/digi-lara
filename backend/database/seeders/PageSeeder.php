<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::truncate();
        Page::insert($this->makePages());
    }

    public function makePages()
    {
        return [
            [
                'user_id' => '1',
                'title' => 'اتاق خبر لاراشاپ',
                'description' => '<p>اتاق خبر لاراشاپ</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'فروش در لاراشاپ',
                'description' => '<p>فروش در لاراشاپ</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'گزارش تخلف در لاراشاپ',
                'description' => '<p>گزارش تخلف در لاراشاپ</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'درباره لاراشاپ',
                'description' => '<p>درباره لاراشاپ</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'رویه‌های بازگرداندن کالا',
                'description' => '<p>رویه‌های بازگرداندن کال</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'شرایط استفاده',
                'description' => '<p>شرایط استفاده</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'حریم خصوصی',
                'description' => '<p>حریم خصوصی</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'نحوه ثبت سفارش',
                'description' => '<p>نحوه ثبت سفارش</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '1',
                'title' => 'رویه ارسال سفارش',
                'description' => '<p>رویه ارسال سفارش</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
    }
}
