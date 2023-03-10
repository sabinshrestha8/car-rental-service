<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookCarRequest;
use App\Http\Requests\UpdateBookCarRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
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
        if (auth()->user()->roles[0]->name === 'Admin') {
            $bookings = Booking::latest()->get();

            if (empty($bookings)) {
                return response([
                    'message' => 'No bookings yet'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response([
                'bookings' => BookingResource::collection($bookings)
            ]);
        }

        if (empty(auth()->user()->booking)) {
            return response([
                'message' => 'No bookings yet'
            ], Response::HTTP_BAD_REQUEST);
        }

        $bookings = auth()->user()->booking()->latest()->get();

        // dd($bookings);

        return response([
            'bookings' => BookingResource::collection($bookings)
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

        $bookedFrom = strtotime($validatedUpdateBooking['booked_from']);

        $bookedTo = strtotime($validatedUpdateBooking['booked_to']);

        $bookingHour = ($bookedTo - $bookedFrom) / 3600;

        if (empty(Booking::where('id', $request->id)->first())) {
            return response([
                'message' => 'Booking with id: ' . $request->id . ' couldn\'t be found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $booking = Booking::where('id', $request->id)->first();

        $car = Car::find($validatedUpdateBooking['car_id']);

        $validatedUpdateBookingResult = [
            ...$validatedUpdateBooking,
            'total_price' => $bookingHour * $car->price
        ];

        $booking->update($validatedUpdateBookingResult);

        return response([
            'booking' => new BookingResource($booking)
        ]);
    }

    public function returnCar($id)
    {
        if (empty(auth()->user()->booking()->where('id', $id)->first())) {
            return response([
                'message' => 'Booking with id: ' . $id . ' couldn\'t be found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $bookings = auth()->user()->booking()->latest()->get();

        if (!$bookings) {
            return response([
                'message' => 'Booking with id: ' . $id . ' couldn\'t be found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $booking = Booking::where('id', $id)->first();

        $booking->update([
            'status' => false
        ]);

        return response([
            'booking' => new BookingResource($booking)
        ]);
    }
}
