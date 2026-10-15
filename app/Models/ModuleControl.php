<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleControl extends Model
{
    protected $table = 'module_controls';

    protected $fillable = [
        'module_key',
        'display_name',
        'icon',
        'parent_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_key', 'module_key');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_key', 'module_key');
    }
}
