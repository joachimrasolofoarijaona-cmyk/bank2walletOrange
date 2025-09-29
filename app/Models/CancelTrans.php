<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelTrans extends Model
{
    use HasFactory;
    protected $table = 'cancel_transfert';
    protected $fillable = [
        'client_id',
        'client_lastname',
        'client_firstname',
        'musoni_account_no',
        'libelle',
        'amount',
        'alias',
        'msisdn',
        'office_name',
        'bank_agent',
        'resourceId',
        'officeId',
        'clientId',
        'savingId',
        'error_code',
        'error_message',
        'reference_cancel'
        
    ];
}
