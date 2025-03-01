<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
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
            DB::beginTransaction();
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
                ->select('id', 'fullName', 'image', 'phoneNumber')
                ->get();
            return $this->returnData($users, 'تمت العملية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'يوجد بعض الاخطاء, يرجى المحاولة لاحقاً']);
        }
    }
    public function getAllUser()
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if (!$user) {
                return $this->returnError(404, 'المستخدم غير موجود');
            }
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'user');
            })
                ->select('id', 'fullName', 'email', 'phoneNumber', 'image')
                ->get();
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
                'role_id'        => 1
            ]);

            $credentials = ['email' => $user->email, 'password' => $request->password];
            // $token = JWTAuth::attempt($credentials);
            // $user->token = $token;

            $role = Role::where('id', '=', 1)->first();
            if (!$role) {
                return $this->returnError(404, 'Role Not found');
            }
            $user->assignRole($role);
            // $user->loadMissing(['roles']);
            // if (!$token) {
            //     return $this->returnError(401, 'ليس لديك صلاحية ');
            // }
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
