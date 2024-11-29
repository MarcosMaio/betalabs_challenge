<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'comment_id',
        'content',
        'edited_at',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
