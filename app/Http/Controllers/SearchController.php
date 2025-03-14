<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{

    use GeneralTrait;
    public function searchUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $search = $request->search;
            $user = auth()->user();

            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }

            $users = User::where('fullName',  'like', '%' . $search . '%')->get();

            DB::commit();
            return $this->returnData($users, 2);
        } catch (\Exception $ex) {
            DB::rollback();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
        }
    }
}
