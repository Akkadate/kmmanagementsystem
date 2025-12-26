<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'NBU Knowledge Base',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of your knowledge base system',
                'order' => 1,
            ],
            [
                'key' => 'site_description',
                'value' => 'Knowledge Management System for North Bangkok University',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'Site Description',
                'description' => 'A short description of your site',
                'order' => 2,
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'general',
                'label' => 'Site Logo',
                'description' => 'Upload your site logo (optional)',
                'order' => 3,
            ],
            [
                'key' => 'items_per_page',
                'value' => '12',
                'type' => 'number',
                'group' => 'general',
                'label' => 'Items Per Page',
                'description' => 'Number of items to display per page',
                'order' => 4,
            ],
            [
                'key' => 'home_hero_title',
                'value' => 'ยินดีต้อนรับสู่ระบบจัดการความรู้',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Homepage Hero Title',
                'description' => 'Main heading displayed on the homepage hero section',
                'order' => 5,
            ],
            [
                'key' => 'home_hero_subtitle',
                'value' => 'ค้นหาความรู้ที่คุณต้องการ เรียนรู้สิ่งใหม่ๆ และแบ่งปันประสบการณ์ของคุณ',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Homepage Hero Subtitle',
                'description' => 'Subtitle or description displayed below the main heading',
                'order' => 6,
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'kms@northbkk.ac.th',
                'type' => 'email',
                'group' => 'contact',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'order' => 1,
            ],
            [
                'key' => 'contact_phone',
                'value' => '02-XXX-XXXX',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number',
                'order' => 2,
            ],
            [
                'key' => 'contact_address',
                'value' => 'North Bangkok University, Bangkok, Thailand',
                'type' => 'textarea',
                'group' => 'contact',
                'label' => 'Contact Address',
                'description' => 'Physical address of your organization',
                'order' => 3,
            ],

            // Footer Settings
            [
                'key' => 'footer_text',
                'value' => '© 2025 North Bangkok University. All rights reserved.',
                'type' => 'text',
                'group' => 'footer',
                'label' => 'Footer Text',
                'description' => 'Copyright text displayed in footer',
                'order' => 1,
            ],
            [
                'key' => 'footer_about',
                'value' => 'Knowledge Base System helps staff and students access important information and resources.',
                'type' => 'textarea',
                'group' => 'footer',
                'label' => 'About Text',
                'description' => 'Short about text in footer',
                'order' => 2,
            ],

            // Social Media Links
            [
                'key' => 'social_facebook',
                'value' => null,
                'type' => 'url',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Facebook page URL (optional)',
                'order' => 1,
            ],
            [
                'key' => 'social_twitter',
                'value' => null,
                'type' => 'url',
                'group' => 'social',
                'label' => 'Twitter/X URL',
                'description' => 'Twitter/X profile URL (optional)',
                'order' => 2,
            ],
            [
                'key' => 'social_youtube',
                'value' => null,
                'type' => 'url',
                'group' => 'social',
                'label' => 'YouTube URL',
                'description' => 'YouTube channel URL (optional)',
                'order' => 3,
            ],
            [
                'key' => 'social_line',
                'value' => null,
                'type' => 'url',
                'group' => 'social',
                'label' => 'LINE URL',
                'description' => 'LINE official account URL (optional)',
                'order' => 4,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
