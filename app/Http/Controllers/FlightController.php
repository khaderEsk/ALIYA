<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightRequest;
use App\Models\Flight;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlightController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();

            if (!$user) {
                return $this->returnError(404, 'User Not Found');
            }
            $flights = Flight::where('statingPoint', $request->statingPoint)
                ->where('targetPoint', $request->targetPoint)
                ->with('startingPointGovernorate')
                ->with('targetPointGovernorate')
                ->join('users', 'flights.user_id', '=', 'users.id')
                ->orderBy('users.fullName')
                ->select(
                    'flights.statingPoint',
                    'flights.targetPoint',
                    'flights.numberPassengers',
                    'flights.startingTime',
                    'flights.endingTime',
                    'users.fullName as companyName'
                )
                ->get();
            DB::commit();
            return $this->returnData($flights, 'Operation completed successfully', $flights);
        } catch (\Exception $ex) {
            DB::rollback();
            $httpCode = ($ex->getCode() >= 100 && $ex->getCode() <= 599) ? $ex->getCode() : 500;
            return $this->returnError($httpCode, __('An unexpected error occurred. Please try again later.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getMyFlight()
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'User Not Found');
            }
            $flights = $user->flights()
                ->with('startingPointGovernorate')
                ->with('targetPointGovernorate')
                ->get();
            DB::commit();
            return $this->returnData($flights, 'Operation completed successfully');
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), 'noo');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlightRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'User Not Found');
            }

            $user->flights()->create([
                'targetPoint' => $request->targetPoint,
                'statingPoint' => $request->statingPoint,
                'numberPassengers' => $request->numberPassengers,
                'startingTime' => $request->startingTime,
                'endingTime' => $request->endingTime,
            ]);
            DB::commit();
            return $this->returnData(202, 'Operation completed successfully');
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), 'noo');
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
            $flight = Flight::with([
                'user:id,fullName',
                'startingPointGovernorate:id,name',
                'targetPointGovernorate:id,name',
                'passenger:id,flight_id,numberPassenger'
            ])->findOrFail($id);

            $data = [
                'companyName'                   => $flight->user->fullName,
                'startingStation' => $flight->startingPointGovernorate->name,
                'endingStation'   => $flight->targetPointGovernorate->name,
                'startingTime'               => $flight->startingTime,
                'endingTime'                 => $flight->endingTime,
                'allNumberPassengers'        => $flight->numberPassengers,
                'remainingNumber ' => $flight->numberPassengers - $flight->passenger->count(),
                'reservedNumber ' => $flight->passenger->count(),
                'reservedSeats'          => $flight->passenger->map(
                    fn($p) => ['seatNumber' => $p->numberPassenger]
                )->values()->toArray(),
            ];
            if (!$flight) {
                DB::rollback();
                return $this->returnError(404, 'not found');
            }
            DB::commit();
            return $this->returnData($data, __('backend.operation completed successfully', [], app()->getLocale()));
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), 'Please try again later');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'User Not Found');
            }
            $flights = $user->flights()->where('id', $id)->first();
            if (!$flights)
                return $this->returnError(404, 'not found flight');
            $flights->update([
                'numberPassengers' => isset($request->numberPassengers) ?
                    $request->numberPassengers : $flights->numberPassengers,
                'startingTime' => isset($request->startingTime) ?
                    $request->startingTime : $flights->startingTime,
                'endingTime' => isset($request->endingTime) ?
                    $request->endingTime : $flights->endingTime,
            ]);
            DB::commit();
            return $this->returnData($flights, __('backend.operation completed successfully', [], app()->getLocale()));
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), 'Please try again later');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();

            if (!$user) {
                return $this->returnError(404, 'User Not Found');
            }

            $flight = $user->flights()->where('id', $id)->first();

            if (!$flight) {
                return $this->returnError(404, 'Flight Not Found');
            }

            $flight->delete();
            DB::commit();
            return $this->returnData(200, __('backend.operation completed successfully', [], app()->getLocale()));
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), 'Please try again later');
        }
    }
}
