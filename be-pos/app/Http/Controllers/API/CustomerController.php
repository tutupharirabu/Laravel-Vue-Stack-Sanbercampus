<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index()
    {
        try {
            $customers = Customer::all();
            return response()->json([
                'status' => 'success',
                'data' => $customers,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch customers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email|unique:customers,email',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',
        ]);

        try {
            $customer = Customer::create([
                'email' => $request->email,
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $customer,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified customer.
     */
    /**
     * Display the specified customer along with their transactions.
     */
    public function show($id)
    {
        try {
            // Fetch customer with their transactions
            $customer = Customer::with('transactions')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $customer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found or an error occurred',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|email|unique:customers,email,' . $id . ',customer_id',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',
        ]);

        try {
            $customer = Customer::findOrFail($id);

            $customer->update([
                'email' => $request->email,
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $customer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
