<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{

    public function index()
    {
        try {
            $transactions = Transaction::with(['customer', 'cashier'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Transactions retrieved successfully.',
                'data' => $transactions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve transactions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function pay(Request $request)
    {
        try {
            // Set Midtrans configuration
            Config::$serverKey = config('app.midtrans.server_key');
            Config::$isProduction = config('app.midtrans.is_production');
            Config::$isSanitized = config('app.midtrans.sanitize');
            Config::$is3ds = config('app.midtrans.midtrans_3ds');

            // Generate order ID
            $orderId = Str::uuid();

            // Process cart items
            $items = [];
            $grossAmount = 0;

            foreach ($request->items as $item) {
                $items[] = [
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name']
                ];

                $grossAmount += ($item['price'] * $item['quantity']);
            }

            // Prepare transaction data for Midtrans
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount
            ];

            $transaction = [
                'transaction_details' => $transactionDetails,
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'email' => $request->customer_email ?? 'customer@example.com',
                    'phone' => $request->customer_phone ?? '08111222333'
                ]
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($transaction);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'transaction_id' => 'required|uuid',
            'customer_id' => 'required|uuid|exists:customers,customer_id',
            'cashier_id' => 'required|uuid|exists:users,user_id',
            'amount' => 'required|numeric',
            'items' => 'required|json',
            'status' => 'required|boolean',
        ]);

        try {
            // Simpan transaksi ke database
            $transaction = Transaction::create([
                'transaction_id' => $validatedData['transaction_id'],
                'customer_id' => $validatedData['customer_id'],
                'cashier_id' => $validatedData['cashier_id'],
                'amount' => $validatedData['amount'],
                'items' => $validatedData['items'],
                'status' => $validatedData['status'],
            ]);

            return response()->json([
                'message' => 'Transaction saved successfully.',
                'transaction' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save transaction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
