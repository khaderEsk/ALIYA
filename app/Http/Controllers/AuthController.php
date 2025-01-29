<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Mail\VerfMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        $exist = User::where('email', $request->email)->first();

        if (!$exist->email_verified_at) {
            return $this->returnError(400, 'Please enter  the Verification Code');
        }
        if ($exist && !$token)
            return $this->returnError(401, __('backend.The password is wrong', [], app()->getLocale()));

        if (!$token)
            return $this->returnError(401, __('backend.Account Not found', [], app()->getLocale()));
        $user = auth()->user();
        $user->token = $token;
        $user->loadMissing(['roles']);

        return $this->returnData($user, __('backend.operation completed successfully', [], app()->getLocale()));
    }



    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $random_number = random_int(100000, 999999);
            $mailData = [
                'title' => 'Code login',
                'code' => $random_number,
            ];
            // try {
            Mail::to($request->email)->send(new VerfMail($mailData));
            // } catch (\Exception $e) {
            //     return $this->returnError(400,$e->getMessage());
            // }

            $user = User::create([
                'fullName'       => $request->fullName,
                'email'          => $request->email,
                'password'       => $request->password,
                'phoneNumber'    => $request->phoneNumber,
                'code'           => $random_number
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
                return $this->returnError(401, 'Unauthorized');
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
                return $this->returnSuccessMessage("Logged out successfully", "200");
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

    public function test()
    {
        $user = auth()->user();
        //dispatch(new SendFcmNotification($user->id,"message","title"));
        //        $SERVER_KEY=env('FCM_SERVER_KEY');
        //        $fcm=Http::acceptJson()->withToken($SERVER_KEY)
        //            ->post('https://fcm.googleapis.com/fcm/send',
        //                [
        //                    'to'=>$user->fcm_token,
        //                    'notification'=>
        //                        [
        //                            'title'=>"title",
        //                            'body'=>"message"
        //                        ]
        //                ]);
        $fcm = $this->send($user, "title", "message", 'basic');
        return $fcm;
        //return $this->returnSuccessMessage('operation completed successfully');
    }
}
