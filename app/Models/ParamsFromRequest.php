<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParamsFromRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','from_id', 'to_id', 'is_head'
    ];

    protected $guarded = false;
}
