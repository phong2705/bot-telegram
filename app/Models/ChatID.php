<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatID extends Model
{
    // use HasFactory;
    protected $table='chatinfo';
    protected $primaryKey='id';
    protected $fillable = [
        'chat_id',
    ];
}
