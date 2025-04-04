<?php

namespace App\Http\Controllers\User;

use App\Enums\KYCStatus as EnumsKYCStatus;
use App\Enums\StatusEnum;
use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\KycRequest;
use App\Http\Services\UserService;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Admin\Withdraw;
use App\Models\Core\File;
use App\Models\KycLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Http\Utility\SendNotification;
use App\Jobs\SendMailJob;
use App\Models\Admin;
use Carbon\Carbon;
use App\Traits\Notifyable;
use App\Traits\Fileable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserController extends Controller
{

    protected $userService ,$user;

    use Notifyable ,Fileable;
    public function __construct(){

        $this->userService      = new UserService();

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web')->load(['pendingWithdraws']);
            return $next($request);
        });
    }



    /**
     * View pricing plan
     *
     * @return View
     */
    public function plan () :View{

        return view('user.plan',[

            'meta_data' => $this->metaData(["title"    =>  trans('default.plan')]),
            "plans"     => Package::active()->get()

        ]);
    }


    /**
     * Purchase a  Plan
     *
     * @param string $slug
     * @return RedirectResponse
     */
    public function planPurchase(string $slug) :RedirectResponse{


        $package   = Package::where("slug",$slug)->firstOrfail();
        $response  = $this->userService->createSubscription($this->user,$package);
        $status    = isset($response['status'])
                         ? 'success'
                         : 'error';

        return back()->with(response_status(Arr::get($response,"message",trans("default.something_went_wrong")),$status));
    }



    /**
     * Withdraw request view
     *
     * @param Request $request
     * @return View
     */
    public function withdrawCreate(Request $request) :View{

        return view('user.withdraw.create',[

            'meta_data'  => $this->metaData(['title'=> translate("Withdraw Request")]),
            'methods'    => Withdraw::active()->get(),

        ]);

    }


    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withdrawRequest(Request $request) :RedirectResponse{

        $amount = (session()->get("withdraw_amount"));
        $gwId   = session()->get("gw_id");

        $gateway           = Withdraw::with(['file'])->findOrfail($gwId);
        $error             = $this->validateWithdrawRequest($gateway, $amount);

        if($error !== null)            return back()->with("error",$error);

        $balance = ((int)$this->user->balance);

        $rules = [
            "amount" => ['numeric','gt:0',"max:".$balance],
        ];

        $customRules       = $this->userService->paramValidationRules($gateway->parameters);


        $mergedRules       = array_merge($rules, $customRules);

        $request->validate($mergedRules);

        $response         = response_status(translate("Invalid amount and gateway"),'error');

        if((int)$request->input('amount') == $amount){

            if($balance < $amount){
                return back()->with(response_status("Insufficient funds in user account. Withdrawal request cannot be processed due to insufficient balance. ",'error'));
            }
            $response = $this->userService
                             ->createWithdrawLog($request,$this->user,$gateway,WithdrawStatus::value('PENDING',true));
        }

        session()->forget("withdraw_amount");
        session()->forget("gw_id");
        session()->forget("trx_code");

        return redirect()
                ->route('user.withdraw.report.list')->with($response);
    }


    /**
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function withdrawProcess(Request $request) :View | RedirectResponse{

        $balance = (int)$this->user->balance;

        $request->validate([
            "id"     => ['required','exists:withdraws,id'],
            "amount" => ['numeric','gt:0',"max:".$balance],
        ],[
            'id.required' => translate("Please select a withdraw method"),
            'id.exists'   => translate("Please select a  valid withdraw method"),
        ]);


        $method                    = Withdraw::with(['file'])->findOrfail($request->input("id"));
        $amount                    = (int) $request->input('amount');

        $charge                    = round_amount((float)$method->fixed_charge + ($amount  * (float)$method->percent_charge / 100));
        $amountWithCharge          = convert_to_base($amount + $charge);

        if($balance <  $amountWithCharge){
            return back()->with(response_status("Insufficient funds in user account. Withdrawal request cannot be processed due to insufficient balance. ",'error'));
        }

        $error            = $this->validateWithdrawRequest($method, $amount);

        if($error !== null){
            return back()->with("error",$error);
        }

        $trx = trx_number();

        session()->put("withdraw_amount",$amount);
        session()->put("gw_id",$method->id);
        session()->put("trx_code",$trx);
        return redirect()->route("user.withdraw.preview",$trx);
    }


    public function withdrawPreview(string $trx_code) :View | RedirectResponse{

        $trx              = session()->get("trx_code");
        if($trx_code  !=  $trx ){
            return redirect()->route("user.withdraw.create")->with(response_status("Invalid request","error"));
        }
        $amount           = session()->get("withdraw_amount");
        $gwId             = session()->get("gw_id");
        $method           = Withdraw::with(['file'])->findOrfail($gwId);

        return view('user.withdraw.preview',[
            'meta_data' => $this->metaData(['title'=> translate("Withdraw preview")]),
            'method'    => $method,
            'amount'    => $amount,
        ]);
    }




    /**
     * Validate withdraw request
     *
     * @param Withdraw $method
     * @param integer $amount
     * @return string|null
     */
    public function validateWithdrawRequest(Withdraw $method , int $amount ) :?string {

        $maxRequestLimit = (int) site_settings("max_pending_withdraw",100);
        $pendingRequest  = (int) $this->user?->pendingWithdraws->count();

        if(convert_to_base($amount)  < $method->minimum_amount || convert_to_base($amount) > $method->maximum_amount ){
            return translate('Withdraw amount should be less than ').num_format(number :$method->maximum_amount ,calC:true). " and greter than ".num_format(number :$method->minimum_amount ,calC:true);
        }

        if($maxRequestLimit == $pendingRequest ){
            return translate('Oops! It looks like your withdrawal request has gone over the limit. Please review and try again.');
        }

        return null;
    }



    /**
     * Summary of kycForm
     * @return View|RedirectResponse
     */
    public function kycForm() :View  | RedirectResponse {

        if($this->user->is_kyc_verified   == StatusEnum::true->status()) return redirect()->route('user.home');
        return view('user.kyc_form',[
            'meta_data' => $this->metaData(['title'=> translate("KYC Application form")]),
        ]);
    }




    /**
     * Kyc application request
     *
     * @param KycRequest $request
     * @return RedirectResponse
     */
    public function kycApplication(KycRequest $request) :RedirectResponse {

        if($this->user->is_kyc_verified   == StatusEnum::true->status()) return redirect()->route('user.home');

        $pendingKycs = KycLog::where("user_id",$this->user->id)->pending()->count();

        if($pendingKycs > 0) return back()->with(response_status('You already have a pending KYC request, Please wait for our confirmation','error'));

        $kycLog =   DB::transaction(function() use ($request ) {

                        $kycLog                  = new KycLog();
                        $kycLog->user_id         = $this->user->id;
                        $kycLog->status          = EnumsKYCStatus::value("REQUESTED",true);
                        $kycLog->kyc_data        = (Arr::except($request['kyc_data'],['files']));
                        $kycLog->save();

                        if(isset($request["kyc_data"] ['files'])){
                            foreach($request["kyc_data"] ['files'] as $key => $file){
                                $response = $this->storeFile(
                                    file        : $file,
                                    location    : config("settings")['file_path']['kyc']['path'],
                                );
                                if(isset($response['status'])){
                                    $file = new File([
                                        'name'      => Arr::get($response, 'name', '#'),
                                        'disk'      => Arr::get($response, 'disk', 'local'),
                                        'type'      => $key,
                                        'size'      => Arr::get($response, 'size', ''),
                                        'extension' => Arr::get($response, 'extension', ''),
                                    ]);

                                    $kycLog->file()->save($file);
                                }
                            }
                        }

                        $route          =  route("admin.kyc.report.details",$kycLog->id);
                        $admin          = get_superadmin();
                        $code           = [
                            "name"          =>  $this->user->name,
                            "time"          =>  Carbon::now(),
                        ];

                        $notifications = [

                            'database_notifications' => [
                                'action' => [SendNotification::class, 'database_notifications'],
                                'params' => [
                                    [ $admin, 'KYC_APPLIED', $code, $route ],

                                ],
                            ],



                            'email_notifications' => [
                                'action' => [SendMailJob::class, 'dispatch'],
                                'params' => [
                                    [$admin,'KYC_APPLIED',$code],

                                ],
                            ],
                            'sms_notifications' => [
                                'action' => [SendMailJob::class, 'dispatch'],
                                'params' => [[$admin,'KYC_APPLIED',$code]],
                            ],

                        ];

                        $this->notify($notifications);
                        return $kycLog ;
                    });
        return redirect()->route("user.kyc.report.list")->with(response_status('KYC application submitted! Verification in progress. We will notify you upon completion. Thank you for your patience'));
    }
}
