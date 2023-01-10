<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookCarRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Bool_;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    public function bookCar(BookCarRequest $request)
    {
        $validatedBookCar = $request->validated();

        $booking = Booking::where([
            ['status', true],
            ['car_id', $validatedBookCar['car_id']],
        ])->first();

        if (!$booking) {
            $validatedBookCarResult = [...$validatedBookCar, 'status' => true, 'user_id' => auth()->user()->id];

            $bookedCar = Booking::create($validatedBookCarResult);

            return response([
                'Booking' => new BookingResource($bookedCar)
            ], Response::HTTP_CREATED);
        }

        return response([
            'message' => 'Car already booked'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function bookingHour()
    {
        $booking = Booking::where('status', true)->first();

        $bookingHours = strtotime($booking->booked_to) - strtotime($booking->booked_from);

        $calculate = (int) (($bookingHours / (1000 * 60)) % 24) / 60;

        // dd($booking->car()->first()->price);

        return round($calculate * $booking->car()->first()->price, 2);
    }
}
