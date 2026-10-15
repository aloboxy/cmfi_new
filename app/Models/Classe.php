<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Alias for SchoolClass to avoid reserved keyword confusion - maps to same table
class Classe extends SchoolClass
{
    protected $table = 'class';
}
