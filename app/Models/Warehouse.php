<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'warehouse_type',
        'address',
        'is_default',
        'is_active',
    ];
}
