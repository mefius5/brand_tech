<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supervisor_user_id',
        'street',
        'building_number',
        'city',
        'zip'
    ];

    public $timestamps = false;
}