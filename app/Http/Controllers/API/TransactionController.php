<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // public function all(Request $request)
    // {
    //     $id = $request->input('id');
    //     $limit = $request->input('limit', 6);
    //     $status = $request->input('status');

    //     if ($id) {
    //         $transaction = Transaction::with(['items.service'])->find($id);

    //         if ($transaction) {
    //             return ResponseFormatter::success(
    //                 $transaction,
    //                 'Data transaksi berhasil diambil'
    //             );
    //         } else {
    //             return ResponseFormatter::error(
    //                 null,
    //                 'Data transaksi tidak ada '
    //             );
    //         }
    //     }

    //     $transaction = Transaction::with(['items.service'])->where('users_id', Auth::user()->id);

    //     if ($status) {
    //         $transaction->where('status', $status);
    //     }

    //     return ResponseFormatter::success(
    //         $transaction->paginate($limit),
    //         'Data list transaksi berhasil diambil',
    //     );
    // }
    // static function getLeastBusyTherapist()
    // {
    //     if (Transaction::count() == 0) {
    //         return User::where('roles', '=', 'therapist')->first();
    //     } else {
    //         $joblessTherapists = DB::table("users")
    //             ->select()
    //             ->whereNotIn('users.id', DB::table("transactions")->select('therapist_id'))
    //             ->where('roles', '=', 'therapist')
    //             ->get();

    //         if ($joblessTherapists->count() == 0) {
    //             $leastBusyTherapist = DB::table("users")
    //                 ->join('transactions', 'users.id', "=", "transactions.therapist_id")
    //                 ->select('users.id', DB::raw('COUNT(transactions.therapist_id) as jobcount'))
    //                 ->groupBy('users.id')
    //                 ->orderBy('jobCount', 'asc')
    //                 ->first();

    //             return User::where('id', '=', $leastBusyTherapist->id)->first();
    //         } else {
    //             return $joblessTherapists->first();
    //         }
    //     }
    // }

    // public function checkout(Request $request)
    // {
    //     $request->validate([
    //         'therapist_id' => 'nullable',
    //         'total_price' => 'required',
    //         'extra_price' => 'required',
    //         'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED'
    //     ]);

    //     try {
    //         $user = Auth::user();
    //         if ($user->roles != "therapist") {
    //             $therapistId = TransactionController::getLeastBusyTherapist()->id;
    //             $userId = Auth::user()->id;
    //             Transaction::create([
    //                 'users_id' => $userId,
    //                 'therapist_id' => $therapistId,
    //                 'address' => $request->address,
    //                 'total_price' => $request->total_price,
    //                 'extra_price' => $request->extra_price,
    //                 'status' => $request->status
    //             ]);
    //             $transaction = Transaction::where('users_id', $userId)->first();
    //             return ResponseFormatter::success(['report' => $transaction], 'Transaction Created');
    //         } else {
    //             return ResponseFormatter::error(null, "therapist cannot create transactions.");
    //         }
    //     } catch (Exception $err) {
    //         return ResponseFormatter::error([
    //             'message' => 'Something went wrong',
    //             'error' => $err
    //         ], 'Server Error', 500);
    //     }
    // }

    public function getLeastBusyTherapist()
    {
        if (Transaction::count() == 0) {
            return User::where('roles', '=', 'therapist')->first();
        } else {
            $joblessTherapists = User::whereNotIn('id', Transaction::select('therapist_id'))
                ->where('roles', '=', 'therapist')
                ->get();

            if ($joblessTherapists->count() == 0) {
                $leastBusyTherapist = User::join('transactions', 'users.id', '=', 'transactions.therapist_id')
                    ->select('users.id', DB::raw('COUNT(transactions.therapist_id) as jobcount'))
                    ->groupBy('users.id')
                    ->orderBy('jobcount', 'asc')
                    ->first();

                return User::where('id', '=', $leastBusyTherapist->id)->first();
            } else {
                return $joblessTherapists->first();
            }
        }
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'therapist_id' => 'nullable',
            'total_price' => 'required',
            'extra_price' => 'required',
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED'
        ]);

        try {
            $user = Auth::user();
            if ($user->roles != "therapist") {
                $therapistId = $this->getLeastBusyTherapist()->id;
                $userId = $user->id;
                Transaction::create([
                    'user_id' => $userId,
                    'therapist_id' => $therapistId,
                    'address' => $request->address,
                    'total_price' => $request->total_price,
                    'extra_price' => $request->extra_price,
                    'status' => $request->status
                ]);
                $transaction = Transaction::where('user_id', $userId)->first();
                return ResponseFormatter::success(['report' => $transaction], 'Transaction Created');
            } else {
                return ResponseFormatter::error(null, "Therapists cannot create transactions.");
            }
        } catch (Exception $err) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $err
            ], 'Server Error', 500);
        }
    }
}
