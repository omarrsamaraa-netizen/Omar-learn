<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
     protected $fillable = [
        'name',
    ];
    
}
$user = User::find(1);

$user->role_id = 1;

$user->save();
