<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Flight;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SuperAdminController extends Controller
{

    use GeneralTrait;

    private $uploadPath = "assets/images/users";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
                ->select('id', 'fullName', 'image', 'phoneNumber')
                ->get()->map(function ($user) {
                    return [
                        'id'          => $user->id,
                        'fullName'    => $user->fullName,
                        'image'       => $user->image,
                        'phoneNumber' => $user->phoneNumber,
                        'isBlocked'   => $user->isBlocked(), // ✅ إضافة حالة الحظر
                    ];
                });
            return $this->returnData($users, 'تمت العملية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
        }
    }
    public function getAllUser()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'user');
            })
                ->select('id', 'fullName', 'image', 'phoneNumber')
                ->get()->map(function ($user) {
                    return [
                        'id'          => $user->id,
                        'fullName'    => $user->fullName,
                        'image'       => $user->image,
                        'phoneNumber' => $user->phoneNumber,
                        'isBlocked'   => $user->isBlocked(), // ✅ إضافة حالة الحظر
                    ];
                });
            return $this->returnData($users, 'تمت العملية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
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
    public function store(CompanyRequest $request)
    {
        DB::beginTransaction();
        try {
            $image = null;
            if (isset($request->image)) {
                $image = $this->saveImage($request->image, $this->uploadPath);
            }

            $user = User::create([
                'fullName'       => $request->fullName,
                'email'          => $request->email,
                'password'       => $request->password,
                'phoneNumber'    => $request->phoneNumber,
                'image'          => $image,
                'email_verified_at' => now(),
                'role_id'        => 1
            ]);

            $credentials = ['email' => $user->email, 'password' => $request->password];

            $role = Role::where('id', '=', 1)->first();
            if (!$role) {
                return $this->returnError(404, 'Role Not found');
            }
            $user->assignRole($role);
            DB::commit();
            return $this->returnData($user, 'تم إضافة شركة النقل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong, please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $admin = auth()->user();
            if (!$admin) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $company = User::where('id', $id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'admin');
                })
                ->with(['flights' => function ($query) {
                    $query->select(
                        'id',
                        'startingPoint',
                        'targetPoint',
                        'numberPassengers',
                        'startingTime',
                    )
                        ->with([
                            'startingPointGovernorate:id,name',
                            'targetPointGovernorate:id,name',
                            'passenger:id,flight_id,numberPassenger'
                        ])
                        ->get(); // 10 رحلات لكل صفحة
                }])
                ->select('id', 'fullName', 'isBlocked')
                ->findOrFail($id);

            return $this->returnData($admin, 'تمت العملية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $admin = auth()->user();
            if (!$admin) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $company = User::where('id', $id)->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->first();

            if (!$company) {
                return $this->returnError(404, 'الشركة غير موجودة');
            }
            $image = null;
            if (isset($request->image)) {
                $image = $this->saveImage($request->image, $this->uploadPath);
                $this->deleteImage($company->image);
            }
            $company->update([
                'fullName' => isset($request->fullName) ? $request->fullName : $company->fullName,
                'email' => isset($request->email) ? $request->email : $company->email,
                'phoneNumber' => isset($request->phoneNumber) ? $request->phoneNumber : $company->phoneNumber,
                'image' => isset($request->image) ? $image : $company->image,
            ]);
            DB::commit();
            return $this->returnData($company, 'تمت العملية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
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
            $user = User::where('id', $id)->first();
            if (!$user) {
                return $this->returnError(404, 'الشركة غير موجود');
            }
            $user->delete();
            $user->flights()->delete();
            $user->block()->delete();
            DB::commit();
            return $this->returnData('تم حذف الشركة بنجاح', 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
