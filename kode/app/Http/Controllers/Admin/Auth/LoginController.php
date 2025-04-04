<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\LoginKeyEnum;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{

    /**
     * Show login form
     *
     * @return View
     */
    public function login(): View{
        return view("admin.auth.login",[ 'title' => 'Login']);
    }


    /**
     * Authenticate  user
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request): RedirectResponse{

        $response = response_status('Server Error!! Please Reload Then Try Again ','error');

        try {
            $this->validateLogin($request);

            if (Auth::guard('admin')->attempt([
                                                $this->username($request->input('login')) => $request->input('login'),
                                                "password"=>$request->input('password')
                                             ])){

                $authUser = auth_user();

                $authUser->last_login = Carbon::now();
                $authUser->save();


                $intendedUrl = session()->get('url.intended');
                if ($intendedUrl && str_contains($intendedUrl, '/admin')) {
                    return redirect()->intended()->with(response_status('Successfully Logged'));
                }


                return redirect()->route('admin.home')->with(response_status('Successfully Logged'));
            }

            $response = response_status('Invalid Credential','error');

        } catch (\Throwable $th) {

        }
        return back()->with($response);

    }

    /**
     * Get username
     *
     * @param string $login
     * @return string
     */
     public function username(string $login): string{

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return LoginKeyEnum::EMAIL->value;
        } elseif (preg_match('/^[0-9]+$/', $login)) {
            return LoginKeyEnum::PHONE_NUMBER->value;
        }
        return LoginKeyEnum::USERNAME->value;;
    }

    /**
     * Validate the admin login request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     *
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'login'              => 'required|string',
            'password'           => 'required|string',
        ],[
            'login.required'     => ucfirst($this->username($request)). translate(' Feild Is Required'),
            'password.required'  => translate("Password Feild Is Required")
        ]);

    }

    /**
     * Logout
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse{
        Auth::guard('admin')->logout();
        return redirect('/admin');
    }
}
