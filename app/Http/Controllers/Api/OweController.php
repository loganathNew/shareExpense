<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expense;
use App\User;
use App\ExpenseSplit;
use App\ExpenseOwe;
use DB;

class OweController extends Controller
{
    //
    public function index($user_id = 'all')
    {
        $users = User::when($user_id != "all", function ($query) use ($user_id) {
            return $query->where('id', $user_id);
        })
            ->get();
        $expense_owes = [];
        foreach ($users as $user) {
            $expense_owes[$user->name] = ExpenseOwe::select(DB::raw("
                        expense_owes.owe_amount,
                        users.name as owe_to"))
                ->join('users', 'users.id', '=', 'expense_owes.user_id')
                ->where('expense_owes.friend_id', "=", $user->id)
                ->get();
        }

        return $expense_owes;
    }
}
