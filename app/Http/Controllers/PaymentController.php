<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Booking;
use App\Models\Payment;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Store payment data of specific booking
    */
    public function payForBooking(StorePaymentRequest $request, $id)
    {
        // $validatedPayment = $request->validate([
        //     'payment_number' => 'required|integer',
        //     'payment_token' => 'required|string',
        // ]);

        $validatedPayment = $request->validated();

        $validatedPayment = [
            ...$validatedPayment,
            'booking_id' => $id,
            'user_id' => auth()->user()->id
        ];

        $booking = Booking::where('id', $id)->first();

        if ($booking->status === 0) {
            return response([
                'message' => 'Payment already done'
            ], Response::HTTP_BAD_REQUEST);
        }

        $storePayment = Payment::firstOrCreate($validatedPayment);

        return response([
            'message' => 'Payment was successfully',
            'payment' => PaymentResource::make($storePayment)
        ], Response::HTTP_CREATED);
    }

    /**
     * Get payment of specific booking
     */
    public function retrieveUserPayment($id)
    {
        $booking = Booking::where('id', $id)->first();

        $retrievedPayment = $booking->payment()->get();

        return response([
            'payment' => PaymentResource::collection($retrievedPayment)
        ]);
    }

    /**
     * Get all payments of authenticated user
     */
    public function retrieveUserPayments()
    {
        if (auth()->user()->roles[0]->name === 'Admin') {
            $payments = Payment::latest()->get();

            if (empty($payments)) {
                return response([
                    'message' => 'No payments yet'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response([
                'payments' => PaymentResource::collection($payments)
            ]);
        }

        $payments = auth()->user()->payments()->latest()->get();

        return response([
            'payment' => PaymentResource::collection($payments)
        ]);
    }
}
