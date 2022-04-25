<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expense;
use App\User;
use App\ExpenseOwe;
use App\ExpenseSplit;
use Illuminate\Support\Facades\Validator;
use DB;

class ExpenseController extends Controller
{
    //Store expense detail And Edit expense detail
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            //Validtaion
            $validator = Validator::make($request->all(), [
                'expense' => 'required|string',
                'user_id' => 'required',
                'amount' => 'required|numeric',
                'type' => ["required", "regex:(EQUAL|EXACT|PERCENT)"],
                'friends.*.id' => "required",
                'friends.*.value' => "required",
            ]);

            if ($validator->fails()) :
                return response()->json($validator->errors(), 200);
            endif;

            //Variable Assign
            $expense_id = $request->expense_id;
            $user_id = $request->user_id;
            $friends = $request->friends;
            $amount = $request->amount;
            $total_splits = count($friends) + 1;
            $type = $request->type;

            //Check type
            if (!in_array($type, array("EQUAL", "EXACT", "PERCENT"))) :
                $message = ['error' => "Only allow EQUAL,EXACT,PERCENT types"];
                return response()->json($message, 200);
            endif;

            //Save Expense data
            if ($expense_id) :
                $expense = Expense::findOrFail($expense_id);
                $expense->splits()->delete();
            else :
                $expense = new Expense();
            endif;
            $data = $request->all();
            $expense->fill($data);
            $expense->save();


            //Save ExpenseSplit data
            $check_values = $this->saveExpenseSplit($friends, $expense, $user_id, $type, $amount, $total_splits);

            //check the amount with total splitted amount
            $total_amount = array_sum($check_values);
            if ($total_amount > $amount) {
                DB::rollback();
                $message = ['error' => "Enter equal split amounts"];
                return response()->json($message, 200);
            }

            //Owe Calculation
            $this->oweCalculation($friends, $user_id);

            //Store all data to DB
            DB::commit();
            $message = ['success' => "Expense Details Saved Successfuly"];
            return response()->json($message, 200);
        } catch (\Exception $e) {
            DB::rollback();

            $message = ['error' => $e->getMessage()];
            return response()->json($message, 500);
        }
    }

    public function saveExpenseSplit($friends, $expense, $user_id, $type, $amount, $total_splits)
    {
        $check_values = [];
        foreach ($friends as $key => $friend) {
            $split = new ExpenseSplit();
            $split->expense_id = $expense->id;
            $split->friend_id = $friend['id'];
            $split->user_id = $user_id;
            if ($type == "EXACT") :
                $split->split_amount = $friend['value'];
                $check_values[] = $friend['value'];
            elseif ($type == "PERCENT") :
                $round_amount = $amount * $friend['value'] / 100;
                if ($key == 0) :
                    $round_amount = round($round_amount, 2);
                endif;
                $split->split_amount = $round_amount;
                $check_values[] = $round_amount;
            else :
                $split_amount = $amount / $total_splits;
                $split->split_amount = $split_amount;
                $check_values = [$amount];
            endif;
            $split->save();
        }
        return $check_values;
    }

    public function oweCalculation($friends, $user_id)
    {
        foreach ($friends as $key => $friend) {
            $friend_id = $friend['id'];

            $all_amount[$user_id] = ExpenseSplit::select(DB::raw("ROUND(SUM(split_amount),2) as split_amount"))
                ->where('user_id', $user_id)->where('friend_id', $friend_id)->groupBy('user_id')
                ->value('split_amount');
            $all_amount[$friend_id] = ExpenseSplit::select(DB::raw("ROUND(SUM(split_amount),2) as split_amount"))
                ->where('user_id', $friend_id)->where('friend_id', $user_id)->groupBy('user_id')
                ->value('split_amount');

            $diff_value = abs($all_amount[$friend_id] - $all_amount[$user_id]);

            $expenseOwe = ExpenseOwe::where('user_id', $user_id)->where('friend_id', $friend_id)->first();
            if (!$expenseOwe) :
                $expenseOwe = new ExpenseOwe();
            endif;
            $expenseOwe->user_id = $user_id;
            $expenseOwe->friend_id = $friend_id;
            $expenseOwe->owe_amount = ($all_amount[$user_id] < $all_amount[$friend_id]) ? 0 : $diff_value;
            $expenseOwe->save();

            $expenseOwe1 = ExpenseOwe::where('user_id', $friend_id)->where('friend_id', $user_id)->first();
            if (!$expenseOwe1) :
                $expenseOwe1 = new ExpenseOwe();
            endif;
            $expenseOwe1->user_id = $friend_id;
            $expenseOwe1->friend_id = $user_id;
            $expenseOwe1->owe_amount = ($all_amount[$user_id] < $all_amount[$friend_id]) ? $diff_value : 0;
            $expenseOwe1->save();
        }
    }

    public function expenses($spent_person_id = 'all')
    {
        $users = User::when($spent_person_id != "all", function ($query) use ($spent_person_id) {
                return $query->where('id', $spent_person_id);
            })
            ->get();

        foreach ($users as $user) {
            $expense_splits[$user->name] = ExpenseSplit::select(DB::raw("
                            expenses.user_id as spent_person_id, 
                            expenses.amount as spent_amount, 
                            expense_splits.split_amount as split_amount, 
                            users.name as split_to"))
                ->join('expenses', 'expenses.id', '=', 'expense_splits.expense_id')
                ->join('users', 'users.id', '=', 'expense_splits.friend_id')
                ->where('expense_splits.user_id', $user->id)
                ->get();
        }

        return $expense_splits;
    }
}
