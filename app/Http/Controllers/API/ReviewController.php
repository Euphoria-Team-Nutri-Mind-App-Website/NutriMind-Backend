<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Traits\GeneralTrait;
use App\Models\Doctor;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        if ($request->has('id') && !empty($request->id)) {
            return $this->viewOne($request->id, 'App\Models\Doctor', 'doctors', 'id', true, 'rate');
        }

        return $this->returnError('You are not authorized to access this information.');
    }

    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();

        try {
            $review = Review::where([
                'doctor_id' => $request->doctor_id,
                'patient_id' => $request->patient_id,
            ])->firstOrFail();

            $review->update([
                'rate' => $request->rate,
            ]);
        } catch (ModelNotFoundException $exception) {
            Review::create($request->all());
        }

        $averageRate = Review::where('doctor_id', $request->doctor_id)->avg('rate');
        Doctor::where('id', $request->doctor_id)->update(['rate' => $averageRate]);

        return $this->returnSuccess('Review added successfully.');
    }
}
