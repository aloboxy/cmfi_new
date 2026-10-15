<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $table = 'theme_settings';

    protected $fillable = [
        'border_mode',
        'dark_skin',
        'primary_color',
        'sidebar_bg',
        'sidebar_text',
        'sidebar_icon',
        'sidebar_active',
        'text_dark',
        'idcard_layout',
        'idcard_bg_color',
        'idcard_text_color',
        'idcard_border_color',
        'idcard_show_father',
        'idcard_show_class',
        'idcard_show_section',
        'idcard_show_blood',
        'idcard_show_qrcode',
        'login_button_color',
        'login_hero_title',
        'login_hero_text',
    ];

    protected $casts = [
        'idcard_show_father' => 'boolean',
        'idcard_show_class' => 'boolean',
        'idcard_show_section' => 'boolean',
        'idcard_show_blood' => 'boolean',
        'idcard_show_qrcode' => 'boolean',
    ];
}
