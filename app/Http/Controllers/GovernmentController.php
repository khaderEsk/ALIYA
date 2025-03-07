<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GovernmentController extends Controller
{

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $governments = Governorate::all();
            DB::commit();
            return $this->returnData($governments, 'تمت العملية بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();
            $httpCode = ($ex->getCode() >= 100 && $ex->getCode() <= 599) ? $ex->getCode() : 500;
            return $this->returnError($httpCode, __('An unexpected error occurred. Please try again later.'));
        }
    }

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
