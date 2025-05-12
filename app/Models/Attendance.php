<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'check_in', 'check_out',
        'photo_check_in', 'photo_check_out',
        'check_in_latitude', 'check_in_longitude',
        'check_out_latitude', 'check_out_longitude',
    ];

    // RELATIONS
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
