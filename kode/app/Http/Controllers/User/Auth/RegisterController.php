<?php

namespace App\Http\Controllers\User\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Services\User\AuthService;
use App\Http\Services\UserService;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{


    protected $authService ,$userService;

    /**
     *
     * @return void
     */
    public function __construct()
    {

        $this->authService = new AuthService();
        $this->userService = new UserService();


        $this->middleware(function ($request, $next) {
            if (site_settings('registration') == StatusEnum::false->status()) {
                return redirect()->route('auth.login')->with(response_status('Registration module is currently unavailable!!', 'error'));
            }
            return $next($request);
        });

    }



    /**
     * user registration
     *
     * @param UserRegisterRequest $request
     * @return RedirectResponse
     */
    public function store(UserRegisterRequest $request) :RedirectResponse{


        $response = response_status(translate("Something went wrong!! please try again"),'error');
        try {

            if($request->get('referral_code',null)){
                $refferedBy = User::active()->where('referral_code',$request->input('referral_code',null))->first();
            }

            $user                       =  new User();
            $user->name                 =  $request->input('name');
            $user->username             =  $request->input('username');
            $user->phone                =  $request->input('phone');
            $user->email                =  $request->input('email');
            $user->address              =  $request->input('address',[]);
            $user->password             =  $request->input('password');
            $user->country_id           =  $request->input('country_id');
            $user->referral_id          =  @$refferedBy?->id;
            $user->save();

            $package = Package::active()
                                ->where('id',site_settings('signup_bonus',-1))
                                ->first();

            if($package)    $this->userService->createSubscription( $user ,  $package , "Sign up bonus");

            Auth::guard('web')->loginUsingId($user->id);
            return redirect()->route('user.home');

        } catch (\Exception $ex) {
            $response = response_status(strip_tags($ex->getMessage(),'error'));

        }

        return back()->with( $response);

    }


    /**
     * Show Registration Form
     *
     * @return View
     */
    public function create() :View{

        return view('user.auth.register',[
            'meta_data'=> $this->metaData(['title' => translate("Register")]),
        ]);
    }
}

