<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //
    protected $fillable = [
        'expense','user_id', 'type', 'amount'
    ];

    public function splits(){
        return $this->hasMany('App\ExpenseSplit');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
