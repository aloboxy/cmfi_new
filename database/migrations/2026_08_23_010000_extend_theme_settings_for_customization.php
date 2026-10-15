<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->string('primary_color', 20)->default('#1D4ED8')->after('border_mode');
            $table->string('sidebar_bg', 20)->default('#0D1B3E')->after('primary_color');
            $table->string('sidebar_text', 20)->default('#C5D0E8')->after('sidebar_bg');
            $table->string('sidebar_icon', 20)->default('#C5D0E8')->after('sidebar_text');
            $table->string('sidebar_active', 20)->default('#F4A61F')->after('sidebar_icon');
            $table->string('text_dark', 20)->default('#111827')->after('sidebar_active');
            $table->string('idcard_layout', 20)->default('vertical')->after('text_dark');
            $table->string('idcard_bg_color', 20)->default('#ffffff')->after('idcard_layout');
            $table->string('idcard_text_color', 20)->default('#333333')->after('idcard_bg_color');
            $table->string('idcard_border_color', 20)->default('#E5E7EB')->after('idcard_text_color');
            $table->boolean('idcard_show_father')->default(0)->after('idcard_border_color');
            $table->boolean('idcard_show_class')->default(1)->after('idcard_show_father');
            $table->boolean('idcard_show_section')->default(1)->after('idcard_show_class');
            $table->boolean('idcard_show_blood')->default(0)->after('idcard_show_section');
            $table->boolean('idcard_show_qrcode')->default(1)->after('idcard_show_blood');
            // Login customization
            $table->string('login_button_color', 20)->default('#1D4ED8')->after('idcard_show_qrcode');
            $table->string('login_hero_title', 255)->nullable()->after('login_button_color');
            $table->string('login_hero_text', 500)->nullable()->after('login_hero_title');
        });
    }

    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color','sidebar_bg','sidebar_text','sidebar_icon','sidebar_active','text_dark',
                'idcard_layout','idcard_bg_color','idcard_text_color','idcard_border_color',
                'idcard_show_father','idcard_show_class','idcard_show_section','idcard_show_blood','idcard_show_qrcode',
                'login_button_color','login_hero_title','login_hero_text',
            ]);
        });
    }
};
