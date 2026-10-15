<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $table = 'global_settings';

    protected $fillable = [
        'institute_name',
        'institution_code',
        'reg_prefix',
        'institute_email',
        'address',
        'mobileno',
        'currency',
        'currency_symbol',
        'sms_service_provider',
        'session_id',
        'translation',
        'footer_text',
        'animations',
        'timezone',
        'date_format',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'school_image',
        'cron_secret_key',
    ];
}
