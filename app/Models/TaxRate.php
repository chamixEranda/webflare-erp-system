<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'tax_group_id',
        'name',
        'rate',
        'tax_type',
        'is_active',
    ];
}
