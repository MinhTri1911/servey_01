<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Survey extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'feature',
        'token',
        'status',
        'deadline',
        'description',
        'mail',
    ];

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('sequence');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function setTokenAttribute($value)
    {
        return $this->attributes['token'] = (strlen($value) >= 32) ? $value : md5(uniqid(rand(), true));
    }

    public function getDeadlineAttribute()
    {
        return (!empty($this->attributes['deadline']))
            ? Carbon::parse($this->attributes['deadline'])->format('Y-m-d H:i:s')
            : null;
    }

    public function temps()
    {
        return $this->hasMany(Temp::class);
    }
}
