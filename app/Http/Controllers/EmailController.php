<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailController extends Controller
{
    use GeneralTrait;
    public function verification(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            if ($request->code != $user->code) {
                return $this->returnError(404, 'الكود المدخل خاطئ');
            }
            $user->update([
                'email_verified_at' => now()
            ]);
            DB::commit();
            return $this->returnData(200,"تم تاكيد حسابك بنجاح");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong, please try again.']);
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
