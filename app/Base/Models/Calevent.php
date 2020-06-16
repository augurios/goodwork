<?php

namespace App\Base\Models;

use App\Base\Models\User;
use Illuminate\Database\Eloquent\Model;

class Calevent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'url', 
        'classes', 
        'startDate',
        'endDate',
        'posted_by',
        'recurent',
    ];

    /**
     * Return the user who created this task.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }
   
}
