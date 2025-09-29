<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscription';

    protected $fillable = [
        'client_id',
        'account_no',
        'msisdn',
        'alias',
        'code_service',
        'key',
        'date_sub',
        'bank_agent',
        'account_status',
        'libelle',
        'officeName',
        'mobile_no',
        'client_cin',
        'client_lastname',
        'client_firstName',
        'client_dob',
    ];
}
