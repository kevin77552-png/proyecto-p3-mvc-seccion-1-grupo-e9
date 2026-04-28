<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MigrationImport extends Model
{
    protected $table = 'migration_imports';

    protected $fillable = ['payload'];

    protected $casts = [
        'payload' => 'array',
    ];
}
