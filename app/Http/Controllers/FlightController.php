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
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $flights = Flight::where('statingPoint', $request->statingPoint)
                ->where('targetPoint', $request->targetPoint)
                ->with('startingPointGovernorate')
                ->with('targetPointGovernorate')
                ->join('users', 'flights.user_id', '=', 'users.id')
                ->orderBy('users.fullName')
                ->select(
                    'flights.id',
                    'flights.statingPoint',
                    'flights.targetPoint',
                    'flights.numberPassengers',
                    'flights.startingTime',
                    'flights.endingTime',
                    'users.fullName as companyName'
                )
                ->get();
            DB::commit();
            return $this->returnData($flights, 'تمت العملية بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
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
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $flights = $user->flights()
                ->with('startingPointGovernorate')
                ->with('targetPointGovernorate')
                ->get();
            DB::commit();
            return $this->returnData($flights, 'تمت العملية بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
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
                return $this->returnError(404, 'المستخدم غير موجود');
            }

            $user->flights()->create([
                'targetPoint' => $request->targetPoint,
                'statingPoint' => $request->statingPoint,
                'numberPassengers' => $request->numberPassengers,
                'startingTime' => $request->startingTime,
                'endingTime' => $request->endingTime,
            ]);
            DB::commit();
            return $this->returnData(202, 'تمت العملية بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
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
                return $this->returnError(404, 'المستخدم غير موجود');
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
                'reservedSeats'          => $flight->passenger->pluck('numberPassenger')->toArray(),
            ];
            if (!$flight) {
                DB::rollback();
                return $this->returnError(404, 'الرحلة غير موجودة');
            }
            DB::commit();
            return $this->returnData($data, 'تمت العملية بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();

            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
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
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $flights = $user->flights()->where('id', $id)->first();
            if (!$flights)
                return $this->returnError(404, 'الرحلة غير موجودة');
            $flights->update([
                'numberPassengers' => isset($request->numberPassengers) ?
                    $request->numberPassengers : $flights->numberPassengers,
                'startingTime' => isset($request->startingTime) ?
                    $request->startingTime : $flights->startingTime,
                'endingTime' => isset($request->endingTime) ?
                    $request->endingTime : $flights->endingTime,
            ]);
            DB::commit();
            return $this->returnData($flights, 'تمت العملية بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();

            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
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
                return $this->returnError(404, 'المستخدم غير موجود');
            }

            $flight = $user->flights()->where('id', $id)->first();

            if (!$flight) {
                return $this->returnError(404, 'الرحلة غير موجودة');
            }

            $flight->delete();
            DB::commit();
            return $this->returnData(200, 'تمت العملية بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();

            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
        }
    }
}
