<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBrand extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'company_id',
        'name',
        'image',
        'is_active',
    ];
}
