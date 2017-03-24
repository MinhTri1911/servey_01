<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'answer_id',
        'sender_id',
        'recevier_id',
        'content',
        'email',
        'name',
    ];

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recevier()
    {
        return $this->belongsTo(User::class, 'recevier_id');
    }
}
