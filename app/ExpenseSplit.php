<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseSplit extends Model
{
    //
    protected $fillable = [
        'expense_id', 'user_id', 'friend_id', 'split_amount'
    ];

    public function expense() {
        return $this->belongsTo('App\Expense');
    }

    public function user() {
        return $this->belongsTo('App\User','friend_id','id');
    }
}
