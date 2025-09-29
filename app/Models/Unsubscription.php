<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unsubscription extends Model
{
    use HasFactory;
    protected $table = 'unsubscription';
    protected $fillable = [
        'client_id',
        'account_no',
        'client_lastname',
        'client_firstname',
        'libelle',
        'alias',
        'msisdn',
        'origin',
        'motif',
        'bank_agent',
        'office_name',
        'date_unsub',
        'client_cin',
        
    ];
}
