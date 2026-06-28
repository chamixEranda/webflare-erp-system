<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'image',
        'is_active',
    ];
}
