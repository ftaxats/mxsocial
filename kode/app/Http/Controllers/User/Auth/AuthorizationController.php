<?php

namespace App\Http\Controllers\User\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Services\User\AuthService;
use App\Models\Core\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AuthorizationController extends Controller
{



    public $authService;

    public function __construct()
    {

        $this->authService = new AuthService();

    }


    /**
     * sms otp verification
     *
     * @return View
     */
    public function otpVerification() :View {

        return view('user.auth.verification',[
            'meta_data'=> $this->metaData(["title" => trans("default.otp_verification")]),
            "route"    => "auth.otp.verify",
        ]);

    }


    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function otpVerify(Request $request) :RedirectResponse {

        $request->validate([
            "otp_code" => ['required', 'numeric'],
        ]);

        $flag = StatusEnum::false->status();
        $responseMessage = response_status('The provided OTP does not exist. Please consider requesting a new OTP', 'error');

        try {
            $identification = session()->get("user_identification");

            if ($this->isValidIdentification($identification) && $this->processVerification($request, $identification)) {
                $flag = StatusEnum::true->status();
                $responseMessage = ('Verification process successfully completed. Your account is now verified and ready for use. Thank you for confirming your details with us.');
            }
        } catch (\Exception $ex) {
            $responseMessage = response_status(strip_tags($ex->getMessage()), 'error');
        }

        return ($flag || \auth_user('web')) == StatusEnum::true->status()
                        ? redirect()->route('user.home')->with($responseMessage)
                        : redirect()->back()->with($responseMessage);

    }



    /**
     * Verify identifications
     *
     * @param mixed $identification
     * @return boolean
     */
    private function isValidIdentification(mixed $identification) :bool {

         return $identification && is_array($identification) && filled($identification['field']) && filled($identification['value']);
    }


    /**
     * Verification process
     *
     * @param Request $request
     * @param mixed $identification
     * @return bool
     */
    private function processVerification(Request $request, mixed $identification): bool {

        return DB::transaction(function () use ($request, $identification) {

            $response = false;
            $user = User::where($identification['field'], $identification['value'])->firstOrFail();
            $otp  = $user->otp->where("otp", $request->input("otp_code"))->first();

            if ($this->isValidOtp($otp)) $response = $this->completeVerification($user, $otp, $identification);

            return $response;
        });
    }


    /**
     * Verify otp code
     *
     * @param Otp|null $otp
     * @return boolean
     */
    private function isValidOtp(?Otp $otp) :bool {
        return $otp && $otp->expired_at > Carbon::now();
    }


    /**
     * Complete  verification route
     *
     * @param User $user
     * @param Otp $otp
     * @param array $identification
     * @return boolean
     */
    private function completeVerification(User $user,Otp $otp, array $identification) :bool {

        $verificationType = $identification['field'] == "email" ? "email" : "sms";

        if ($verificationType == 'email') {
            $user->email_verified_at = Carbon::now();
            $user->save();
        } else {
            Auth::guard('web')->loginUsingId($user->id);
        }

        session()->forget('otp_expire_at');
        session()->forget('user_identification');
        $otp->delete();

        return true;

    }

    /**
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function otpResend() :RedirectResponse {


        $responseMessage =  response_status('Resending OTP failed. We encountered an issue. Please try again later or contact support for assistance.','error');

        try {

            $identification = session()->get("user_identification");

            if($identification
                && is_array($identification)
                && isset($identification['field'], $identification['value'])
                && session()->get("otp_expire_at",Carbon::now()) <= Carbon::now()
            ){
                $user = User::with(['otp'])
                                 ->where(Arr::get($identification,'field') ,Arr::get($identification,'value'))->firstOrfail();
                $type = $identification['field'] == "email" ? "email":"sms";

                $this->authService->otpConfiguration($user,$type);

                $responseMessage =  response_status('The One-Time Passcode (OTP) has been successfully reissued');

            }

        } catch (\Exception $ex) {
            $responseMessage =  response_status(strip_tags($ex->getMessage()),'error');

        }
        return back()->with( $responseMessage);
    }
}
