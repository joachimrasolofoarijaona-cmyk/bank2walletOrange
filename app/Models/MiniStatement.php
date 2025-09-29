<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniStatement extends Model
{
    use HasFactory;

    protected $table = 'mini_statement';

    protected $fillable = [
        'client_id',
        'client_lastname',
        'client_firstname',
        'musoni_account_no',
        'libelle',
        'alias',
        'msisdn',
        'operator_code',
        'request_id',
        'request_token',
        'request_type',
        'affiliate_code',
        'orange_account_no',
        'reason',
        'acep_responde_code',
        'acep_responde_message',
        'office_name',
    ];
}
