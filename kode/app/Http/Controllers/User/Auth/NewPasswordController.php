<?php

namespace App\Http\Controllers\User\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Services\User\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View ;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class NewPasswordController extends Controller
{

    private $authService;
    public function __construct()
    {
        $this->authService = new AuthService();
    }


    /**
     * forget password
     *
     * @return View
     */
    public function create():View{

        return view('user.auth.password.forgot_password',[

            'meta_data'=> $this->metaData(["title" => trans('default.reset_passsword')]),

        ]);
    }


    /**
     * forget password
     *
     * @return mixed
     */
    public function store(Request $request):RedirectResponse {

        $request->validate([
            'email' => "required|email|exists:users,email"
        ]);

        $user      = User::where('email',$request->input('email'))->firstOrfail();
        $response  =  $this->authService->sendOtp($user);
        $request->session()->flash('success', translate("Check your email a code sent successfully for verify reset password process !! You Need To Verify Your Account!!"));

        session()->put("user_reset_password_email",$user->email);
        return redirect()->route("auth.password.verify");

    }



    /**
     * return verification route
     *
     * @return View
     */
    public function verify() :View{

        return view("user.auth.verification",[
            'meta_data'=> $this->metaData(["title" => trans('default.verify_email')]),
            "route"    => "auth.password.verify.code",
        ]);
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyCode(Request $request) :mixed {


        $request->validate([
            'otp_code' => "required|numeric",
        ]);

        $response  = response_status("The provided code does not exist.",'error');

        $user      = User::with("otp")
                      ->where('email',session()->get("user_reset_password_email"))
                      ->firstOrfail();

        $otp       = $user->otp->where("otp", $request->input("otp_code"))->first();

        if($otp){
            session()->put("user_reset_password_code",$request->input("otp_code"));
            return redirect()->route('auth.password.reset');
        }

        return redirect()->back()->with($response);
    }

    /**
     * reset view
     *
     * @return View
     */
    public function resetPassword () :View{

        return view("user.auth.password.reset_password",[
            'meta_data'=> $this->metaData(["title" => trans('default.reset_passsword')]),
        ]);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function updatePassword(Request $request) :RedirectResponse {


        $rules['password']        =  ["required","confirmed",Password::min(5)];

        if(site_settings('strong_password') == StatusEnum::true->status()){

            $rules['password']    =  ["required","confirmed",Password::min(8)
                                        ->mixedCase()
                                        ->letters()
                                        ->numbers()
                                        ->symbols()
                                        ->uncompromised()
                                    ];
        }

        $request->validate($rules);
        $response = response_status("Invalid otp !! please verify your otp again!!",'error');
        $user = User::with("otp")
                      ->where('email',session()->get("user_reset_password_email"))
                      ->firstOrfail();

        $otp  = $user->otp->where("otp", session()->get("user_reset_password_code"))->first();
        if($otp) {
            $response         = response_status("Your Password Has Been Updated!!");
            $user->password   = $request->input('password');
            $user->save();
            $otp->delete();
            session()->forget("user_reset_password_code");
            session()->forget("user_reset_password_email");
        }

        return redirect()->route('auth.login')->with($response);

    }

}
