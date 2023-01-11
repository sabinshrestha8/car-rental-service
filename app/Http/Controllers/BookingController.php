<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookCarRequest;
use App\Http\Requests\UpdateBookCarRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Bool_;
use Symfony\Component\HttpFoundation\Response;
use TheSeer\Tokenizer\Exception;

class BookingController extends Controller
{
    public function bookCar(BookCarRequest $request)
    {
        $validatedBookCar = $request->validated();

        $booking = Booking::where([
            ['status', true],
            ['car_id', $validatedBookCar['car_id']],
        ])->first();

        if ($booking) {
            return response([
                'message' => 'Car already booked'
            ], Response::HTTP_BAD_REQUEST);
        }

        $bookedFrom = new Carbon($validatedBookCar['booked_to']);

        $bookedTo = new Carbon($validatedBookCar['booked_from']);

        $bookingHour = (int) $bookedFrom->diff($bookedTo)->format('%H');

        $car = Car::find($validatedBookCar['car_id']);

        if (!$car) {
            return response([
                'message' => 'Car not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $validatedBookCarResult = [
            ...$validatedBookCar,
            'status' => true,
            'user_id' => auth()->user()->id,
            'total_price' => $bookingHour * $car->price
        ];

        $bookedCar = Booking::create($validatedBookCarResult);

        return response([
            'Booking' => new BookingResource($bookedCar)
        ], Response::HTTP_CREATED);
    }

    public function showBookings()
    {
        $bookings = Booking::where('id', auth()->user()->id)->first();

        return response([
            'bookings' => new BookingResource($bookings)
        ]);
    }

    public function cancelBooking($id)
    {
        Booking::find($id)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function updateBooking(UpdateBookCarRequest $request)
    {
        $validatedUpdateBooking = $request->validated();

        $booking = Booking::where('id', $request->id)->first();

        $booking->update($validatedUpdateBooking);

        return response([
            'booking' => new BookingResource($booking)
        ]);
    }
}
