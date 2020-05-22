<?php

namespace App\Ticket\Models;

use App\Base\Models\User;
use App\Base\Models\Comment;
use App\Base\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int id
 * @property string name
 * @property int posted_by
 */
class Ticket extends Model
{
    protected $fillable = ['name', 'content', 'raw_content', 'posted_by', 'archived', 'public','draft', 'discussionable_type', 'discussionable_id', 'subject_id'];

    protected $appends = ['date'];

    protected $casts = [
        'posted_by' => 'integer',
        'draft'     => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function getDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('M j') : null;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'ticket_user');
    }

    /**
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @return MorphTo|HasMembers
     */
    public function discussionable(): MorphTo
    {
        return $this->morphTo();
    }
}
