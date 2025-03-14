<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Mail\VerfMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use GeneralTrait;

    private $uploadPath = "assets/images/users";


    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $token = JWTAuth::attempt($credentials);

        // If token generation fails, return an error
        if (!$token) {
            $userExists = User::where('email', $request->email)->exists();
            return $this->returnError(
                401,
                $userExists
                    ? __('backend.The password is wrong', [], app()->getLocale())
                    : __('backend.Account Not found', [], app()->getLocale())
            );
        }
        // Get the authenticated user
        $user = auth()->user();
        if ($user->block)
            return $this->returnError(400, 'الحساب محظور');
        // Check if the email is verified
        if (!$user->email_verified_at) {
            return $this->returnError(400, 'يجب تأكيد حسابك أولاً');
        }
        // Load the user's roles and prepare the response data
        $user->loadMissing('roles');
        $data = [
            'id' => $user->id,
            'fullName' => $user->fullName,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'phoneNumber' => $user->phoneNumber,
            'token' => $token,
            'name_role' => optional($user->roles->first())->name,
            'roles' => optional($user->roles->first())->pivot->role_id,
        ];

        return $this->returnData($data, __('backend.operation completed successfully', [], app()->getLocale()));
    }



    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $image = null;
            if (isset($request->image)) {
                $image = $this->saveImage($request->image, $this->uploadPath);
            }
            $random_number = random_int(100000, 999999);
            $mailData = [
                'title' => 'Code login',
                'code' => $random_number,
            ];
            try {
                Mail::to($request->email)->send(new VerfMail($mailData));
            } catch (\Exception $e) {
                return $this->returnError(400, "البريد الإلكتروني غير موجود");
            }

            $user = User::create([
                'fullName'       => $request->fullName,
                'email'          => $request->email,
                'password'       => $request->password,
                'phoneNumber'    => $request->phoneNumber,
                'code'           => $random_number,
                'image'          => $request->image
            ]);

            $credentials = ['email' => $user->email, 'password' => $request->password];
            $token = JWTAuth::attempt($credentials);
            $user->token = $token;

            $role = Role::where('id', '=', $request->role_id)->first();
            if (!$role) {
                return $this->returnError(404, 'Role Not found');
            }
            $user->assignRole($role);
            $user->loadMissing(['roles']);
            if (!$token) {
                return $this->returnError(401, 'لم يتم المصاقة');
            }
            DB::commit();
            return $this->returnData($user, __('backend.operation completed successfully', [], app()->getLocale()));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong, please try again.']);
        }
    }


    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            try {
                JWTAuth::setToken($token)->invalidate();
                return $this->returnSuccessMessage("تم تسجيل الخروج بنجاح", "200");
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return $this->returnError($e->getCode(), 'some thing went wrongs');
            }
        } else {
            return $this->returnError("400", 'some thing went wrongs');
        }
    }


    public function deleteMyAccount()
    {
        try {
            $user = auth()->user();
            if ($user->image)
                $this->deleteImage($user->image);
            $user->delete();
        } catch (\Exception $ex) {
            return $this->returnError("500", 'Please try again later');
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $fcm_token = $request->fcm_token;
            $user = User::find($user_id);
            if (!$user)
                return $this->returnError('404', 'Not found');
            $user->update([
                'fcm_token' => $fcm_token
            ]);

            return $this->returnData($user, __('backend.operation completed successfully', [], app()->getLocale()));
        } catch (\Exception $e) {
            return $this->returnError("500", 'Please try again later');
        }
    }

}
