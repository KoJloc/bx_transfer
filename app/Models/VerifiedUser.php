<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifiedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'bx_id','full_name', 'image', 'job', 'active'
    ];

    protected $guarded = false;
}
