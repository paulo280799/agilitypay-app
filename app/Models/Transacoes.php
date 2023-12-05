<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transacoes extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'account_sender',
        'account_receiver',
        'sender_id'
    ];
}
