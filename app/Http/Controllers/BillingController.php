<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function __construct()
    {
        return $this->middleware(['auth:sanctum']);
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/'
        ]);

        try {
            $user = auth()->user();

            $user->wallet += $request->amount;
            $user->save();

            return response([
                'message' => 'Amount deposited.',
                'data' => [
                    'amount' => $request->amount
                ]
            ]);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function buyCookies(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $user = auth()->user();

            $wallet = $user->wallet -= $request->quantity;

            if (($wallet) < 0) {
                throw new Exception('Insufficient balance.');
            }

            $wallet -= $request->quantity;
            $user->save();

            return response([
                'message' => 'Bought cookies.',
                'data' => [
                    'cookies' => $request->quantity
                ]
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response([
                'message' => $e->getMessage()
            ], 403);
        }
    }
}
