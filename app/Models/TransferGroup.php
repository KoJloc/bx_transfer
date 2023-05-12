<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\History;

class TransferGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','transfer_group_status', 'message_id', 'rollback_message_id'
    ];

    protected $guarded = false;

}
