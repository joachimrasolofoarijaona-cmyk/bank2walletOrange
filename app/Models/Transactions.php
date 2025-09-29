<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $table = 'transaction';

    protected $fillable = [
        'client_id',
        'client_lastname',
        'client_firstname',
        'musoni_account_no',
        'libelle',
        'alias',
        'msisdn',
        'transaction_ref_no',
        'operator_code',
        'request_id',
        'request_token',
        'request_type',
        'affiliate_code',
        'external_ref_no',
        'mobile_no',
        'mobile_name',
        'mobile_alias',
        'orange_account_no',
        'orange_account_name',
        'transfer_description',
        'currency',
        'amount',
        'charge',
        'transaction_date',
        'udf1',
        'udf2',
        'udf3',
        'acep_responde_code',
        'acep_responde_message',
        'bank_agent',
        'office_name',
        'TransactionId',
        'CBAReferenceNo',
        'resourceId',
        'officeId',
        'clientId',
        'savingId',
        'charge_id',
    ];
}