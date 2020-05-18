<?php

namespace App\Base\Models;

use App\Base\Models\Tag;
use App\Base\Models\User;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'url', 
        'phone', 
        'description',
        'user_id',
        'link_tags'
    ];

    /**
     * Return the user who created this task.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'link_tags');
    }

   

   

}
