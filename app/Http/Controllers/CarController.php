<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Resources\CarResource;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use Symfony\Component\HttpFoundation\Response;

class CarController extends Controller
{
    public function store(StoreCarRequest $request)
    {
        $validatedStoreCar = $request->validated();

        $createdCar = Car::create($validatedStoreCar);

        return response([
            'car' => new CarResource($createdCar)
        ], Response::HTTP_CREATED);
    }

    public function index()
    {
        $getAllCars = Car::latest()->get();

        return response([
            'total' => count($getAllCars),
            'cars' => CarResource::collection($getAllCars)
        ]);
    }

    public function update(UpdateCarRequest $request, Car $car)
    {
        $validatedUpdateCar = $request->validated();

        $car->update($validatedUpdateCar);

        return response([
            'car' => new CarResource($car)
        ]);
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
