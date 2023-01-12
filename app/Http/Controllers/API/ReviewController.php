<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Car;
use App\Models\Review;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Car $car)
    {
        $validatedStoreReview = $request->validated();

        $validatedStoreReviewMod = [
            'customer' => auth()->user()->name,
            ...$validatedStoreReview,
        ];

        $review = new Review($validatedStoreReviewMod);

        $car->reviews()->save($review);

        return response([
            'review' => new ReviewResource($review)
        ], Response::HTTP_CREATED);
    }

    public function update(StoreReviewRequest $request, Car $car, Review $review)
    {
        $validatedStoreReview = $request->validated();

        $review->update($validatedStoreReview);

        return response([
            'review' => new ReviewResource($review)
        ],Response::HTTP_CREATED);
    }

    public function destroy(Car $car, Review $review)
    {
        $review->delete();
        return response(null,Response::HTTP_NO_CONTENT);
    }
}
