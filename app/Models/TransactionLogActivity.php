<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLogActivity extends Model
{
    use HasFactory;

    public $timestamps = false; // car tu ajoutes created_at et updated_at manuellement

    protected $fillable = [
        'transaction_id',
        'user_id',
        'type',
        'libelle',
        'account_no',
        'status',
        'amount',
        'currency',
        'description',
        'ip_address',
        'user_agent',
        'request_payload',
        'response_payload',
        'created_at',
        'updated_at'
    ];
}
