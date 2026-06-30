<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxGroup extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'company_id',
        'name',
        'is_active',
    ];
}
