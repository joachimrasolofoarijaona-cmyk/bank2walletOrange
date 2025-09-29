<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{ 
    use HasFactory;
    protected $table = 'validation';
    protected $fillable = [
        'client_id',
        'mobile_no',
        'om_lastname',
        'om_firstname',
        'om_birthdate',
        'om_cin',
        'office_name',
        'bank_agent',
        'status',
        'ticket',
        'key',
        'account_no',
        'code_service',
        'client_cin',
        'client_firstname',
        'client_lastname',
        'client_dob',
        'request_type',
        'validator',
        'origin',
        'motif_validation',
        'request_status'
        
    ];
}
