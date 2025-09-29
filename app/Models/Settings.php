<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';
    
    protected $fillable = [
        'motif',
        'commentaire',
        'pause',
        'user_name', // To store the name of the user who made the changes
        'status', // To track the status of the service
    ];
}
