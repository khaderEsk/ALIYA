<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassengerRequest;
use App\Models\Flight;
use App\Models\Passenger;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PassengerController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'User Not Found');
            }

            $flight = Flight::find($id);
            if (!$flight) {
                return $this->returnError(404, 'Flight Not Found');
            }

            $request->validate([
                'numberPassenger' => 'required|array',
                'numberPassenger.*' => 'integer|min:1', // Ensure each seat number is a positive integer
            ]);

            $list_passengers = [];
            foreach ($request->numberPassenger as $value) {
                $existingPassenger = Passenger::where('flight_id', $id)
                    ->where('numberPassenger', $value)
                    ->first();

                if ($existingPassenger) {
                    return response()->json([
                        'error' => "Seat number $value is already booked for this flight."
                    ], 400);
                }

                // Add the passenger to the list
                $list_passengers[] = [
                    'numberPassenger' => $value,
                    'flight_id' => $id,
                    'user_id' => $user->id,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Insert all passengers at once
            Passenger::insert($list_passengers);

            DB::commit();
            return $this->returnData([], 'Operation completed successfully', 200);
        } catch (\Illuminate\Validation\ValidationException $ex) {
            DB::rollback();
            return $this->returnError(400, $ex->errors());
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError(500, $ex->getMessage());
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        try {
            DB::beginTransaction();

            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'User Not Found');
            }

            $flight = Flight::find($id);
            if (!$flight) {
                return $this->returnError(404, 'Flight Not Found');
            }
            $passengers = $flight->Passenger()
                ->with(['user' => function ($query) {
                    $query->select('id', 'fullName', 'phoneNumber');
                }])
                ->get();
            DB::commit();
            return $this->returnData($passengers, 'Operation completed successfully');
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError(500, 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
