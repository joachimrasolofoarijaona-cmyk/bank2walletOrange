<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false; // empêche Laravel d'ajouter created_at et updated_at
    protected $table = 'activity_logs';
    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address', 'user_agent', 
    ];
}
