<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
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
}
