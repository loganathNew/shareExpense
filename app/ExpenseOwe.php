<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseOwe extends Model
{
    //
    protected $fillable = [
        'user_id', 'friend_id', 'owe_amount'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function friend()
    {
        return $this->belongsTo('App\User', 'friend_id', 'id');
    }
}
