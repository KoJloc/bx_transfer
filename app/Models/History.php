<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
	
    protected $fillable = [
        'id',
		'entity_ID',
		'attached_entity_ID',
		'entity_type',
		'old_responsible_ID',
		'new_responsible_ID',
		'transfer_group_ID',
		'transfer_status',
		'rollback_status',
		'created_at',
		'updated_at'
    ];

    protected $dates = [
		'created_at',
		'updated_at'
    ];
	
    protected $table = 'histories';
}
