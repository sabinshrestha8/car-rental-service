<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Resources\CarResource;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use Symfony\Component\HttpFoundation\Response;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin')->except(['index', 'searchCar']);
    }

    public function store(StoreCarRequest $request)
    {
        $validatedStoreCar = $request->validated();

        $fileTemp = $request->file('image');
        if ($fileTemp->isValid()) {
            $fileExtension = $fileTemp->getClientOriginalExtension();
            $fileName = Str::random(4) . '.' . $fileExtension;
            $path = $fileTemp->storeAs(
                'public/images',
                $fileName
            );
        }

        $validatedStoreCar['image'] = url(Storage::url($path));

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

        $fileTemp = $request->file('image');
        if (!empty($fileTemp)) {
            $fileExtension = $fileTemp->getClientOriginalExtension();
            $fileName = Str::random(4) . '.' . $fileExtension;
            $path = $fileTemp->storeAs(
                'public/images',
                $fileName
            );

            $validatedUpdateCar['image'] = url(Storage::url($path));

            $car->update($validatedUpdateCar);

            return response([
                'car' => new CarResource($car)
            ]);
        }

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

    public function searchCar(Request $request)
    {
        $search = $request->input('name');

        $searchedCars = Car::Where('name', 'LIKE', "%{$search}%")
            ->orWhere('mileage', 'LIKE', "%{$search}%")
            ->orWhere('horsepower', 'LIKE', "%{$search}%")
            ->get();

        return response([
            'cars' => CarResource::collection($searchedCars)
        ]);
    }
}
