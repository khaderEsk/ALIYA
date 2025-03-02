<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlockController extends Controller
{

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store($id)
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);
            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            if ($user->block) {
                return $this->returnError(501, 'المستخدم محظور');
            }

            $block = Block::create([
                'user_id' => $id,
            ]);
            $block->save();

            DB::commit();
            return $this->returnData($block, 'تم حظر المستخدم بنجاح');
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $block = Block::where('user_id', $id)->first();
            if (!$block) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $block->delete();
            DB::commit();
            return $this->returnData('تم فك الحظر عن المستخدم بنجاح', 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
