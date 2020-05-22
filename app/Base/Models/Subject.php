<?php

namespace App\Base\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];

    public function tickets()
    {
        return $this->hasMany(\App\Base\Models\Ticket::class);
    }
}
