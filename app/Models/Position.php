<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // RELATIONS
    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }
}
