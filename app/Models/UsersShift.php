<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersShift extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'substitute_user_id',
        'temp_changes',
        'date_from',
        'date_to'
    ];

    protected $dates = [
        'date_from',
        'date_to'
    ];

    public $timestamps = false;

    public function shift_user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function substitution_user()
    {
        return $this->belongsTo(User::class, 'substitute_user_id', 'user_id');
    }
}