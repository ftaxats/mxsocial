<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View ;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\User\AuthService;
use App\Models\Admin;
use Illuminate\Support\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class NewPasswordController extends Controller
{

    public function __construct(protected AuthService $authService){
    }


    /**
     * Get forget password view
     *
     * @return View
     */
    public function create(): View{
        return view('admin.auth.forgot_password',['title'=> "Reset Passsword"]);
    }



    /**
     * Store OTP for forget password
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse{

        $request->validate([
            'email' => "required|email|exists:admins,email"
        ]);

        $admin = Admin::where('email',$request->input('email'))->firstOrfail();
        $response =  $this->authService->sendOtp($admin);
        $message     = response_status(Arr::get($response,"message",translate("Mail Configuration Error")) , "error");

        if($response['status']){
            $request->session()->flash('success', translate("Check your email a code sent successfully for verify reset password process !! You Need To Verify Your Account!!"));
            session()->put("password_reset_email",$admin->email);
            return redirect()->route("admin.password.verify");
        }

        return redirect()->back()->with($message);
    }



    /**
     * return verification route
     *
     * @return View
     */
    public function verify() :View{

        return view("admin.auth.verify",[

            'title'=> "Verify Your Email",
        ]);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyCode(Request $request) {

        $request->validate([
            'code' => "required",
        ]);

        $message   = response_status("Invalid Code","error");

        $admin     = Admin::with("otp")
                        ->where('email',session()->get("password_reset_email"))
                        ->firstOrFail();

        $otp       = $admin->otp
                        ->where("otp",$request->input("code"))
                        ->first();

        if($otp){
            session()->put("reset_password_otp",$request->input("code"));
            return redirect()->route('admin.password.reset');
        }

        return redirect()->back()->with($message);
    }

    /**
     * reset view
     *
     * @return View
     */
    public function resetPassword () :View{

        return view("admin.auth.reset",[
            'title'=> "Reset Your Password",
        ]);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function updatePassword(Request $request) :RedirectResponse {

        $request->validate([
            'password' => ['required', 'confirmed', 'min:5']
        ]);


        $response = response_status("Invalid otp !! please verify your otp again!!",'error');
        $admin   = Admin::with("otp")
                    ->where('email',session()->get("password_reset_email"))
                    ->firstOrfail();

        $otp     = $admin->otp()
                    ->where('type','password_reset')
                    ->where('otp',session()->get("reset_password_otp"))
                    ->first();

        if($otp){
            $response         = response_status("Your Password Has Been Updated!!");
            $admin->password  = Hash::make($request->get('password'));
            $admin->save();
            $otp->delete();
            session()->forget("password_reset_email");
            session()->forget("reset_password_otp");
        }

        return redirect()->route('admin.login')->with( $response);
    }

}
