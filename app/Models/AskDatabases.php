<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class AskDatabases extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
     protected $table = 'ask_database';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $fillable = [
        'id',
        'schema',
        'visitor',
        'data',
        'data_convert',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'data_convert' => 'array',
    ];
}
