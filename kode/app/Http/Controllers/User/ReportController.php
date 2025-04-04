<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentMethod;
use App\Models\AffiliateLog;
use App\Models\AiTemplate;
use App\Models\CreditLog;
use App\Models\KycLog;
use App\Models\Package;
use App\Models\PaymentLog;
use App\Models\PostWebhookLog;
use App\Models\Subscription;
use App\Models\TemplateUsage;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{


    protected $user ,$subscription,$accessPlatforms,$webhookAccess;

    public function __construct(){

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            $this->subscription           = $this->user->runningSubscription;
            $this->accessPlatforms        = (array) ($this->subscription ? @$this->subscription->package->social_access->platform_access : []);
            $this->webhookAccess          = @optional($this->subscription->package->social_access)
                                                 ->webhook_access;

            return $next($request);
        });
    }




    /**
     * ai template templates report
     *
     * @return View
     */
    public function templateReport() :View {




        return view('user.report.word_report',[

            'meta_data'       => $this->metaData(['title'=> translate("Templates Reports")]),

            "genarated_words" => TemplateUsage::filter(['template:slug'])
                                  ->where('user_id',$this->user->id)
                                  ->date()
                                  ->sum("total_words"),

            "reports"         => TemplateUsage::with(['template','user'])
                                    ->filter(['template:slug'])
                                    ->where('user_id',$this->user->id)
                                    ->date()
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all()),


            "templates"      => AiTemplate::whereHas("templateUsages")->get(),


        ]);


    }


   /**
     * withdraw report
     *
     * @return View
     */
    public function withdrawReport() :View{


        return view('user.report.withdraw_report',[

            'meta_data'       => $this->metaData(['title'=> translate("Withdraw Reports")]),

            "reports"         =>  WithdrawLog::with(['user','method','currency','file'])
                ->where('user_id',$this->user->id)
                ->search(['trx_code'])
                ->filter(['status'])
                ->date()
                ->latest()
                ->paginate(paginateNumber())
                ->appends(request()->all()),
        ]);


    }


    /**
     * deposit report
     *
     * @return View
     */
    public function depositReport() :View{


        return view('user.report.deposit_report',[

            'meta_data'       => $this->metaData(['title'=> translate("Deposit Reports")]),
            "reports"         => PaymentLog::with(['user','method','method.currency','currency','file'])
                                        ->where('user_id',$this->user->id)
                                        ->search(['trx_code'])
                                        ->filter(['method_id','status'])
                                        ->date()
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all()),

            'methods'         => PaymentMethod::active()->get()



        ]);


    }





    /**
     * subscription Log
     *
     * @return View
     */
    public function subscriptionReport() :View{


        return view('user.report.subscription_report',[

            'meta_data'       => $this->metaData(['title'=> translate("Subscriptions Report")]),

            "reports"         => Subscription::with(['user','package','oldPackage'])
                                    ->where('user_id',$this->user->id)
                                    ->search(['trx_code'])
                                    ->filter(['package:slug'])
                                    ->date()
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all()),


            "packages"       => Package::all(),

        ]);


    }


    /**
     * affiliate Users
     *
     * @return View
     */
    public function affiliateUsers() :View{

        return view('user.report.affiliate_user',[
            'meta_data'       => $this->metaData(['title'=> translate("Affiliate Users")]),
            'affliateUsers'   => User::with(['affiliateLogs','file','country'])
                                        ->where("referral_id",$this->user->id)
                                        ->search(['name','email',"phone"])
                                        ->date()
                                        ->paginate(paginateNumber())
        ]);


    }




    /**
     * affiliate report
     *
     * @return View
     */
    public function affiliateReport() :View{


        return view('user.report.affiliate_report',[

            'meta_data'       => $this->metaData(['title'=> translate("Affiliate Reports")]),

            "reports"         =>  AffiliateLog::with(['user','subscription','subscription.package','referral'])
                                                                    ->where('user_id',$this->user->id)
                                                                    ->search(['trx_code'])
                                                                    ->filter(['referral:username'])
                                                                    ->date()
                                                                    ->latest()
                                                                    ->paginate(paginateNumber())
                                                                    ->appends(request()->all()),


        ]);


    }


    /**
     * transaction report
     *
     * @return View
     */
    public function transactionReport() :View{


        return view('user.report.transaction_report',[


            'meta_data'       => $this->metaData(['title'=> translate("Transaction Reports")]),
            "reports"         => Transaction::with(['user','admin','currency'])
                                    ->where('user_id',$this->user->id)
                                    ->search(['remarks','trx_code'])
                                    ->filter(['trx_type'])
                                    ->date()
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all()),

        ]);


    }


    /**
     * Credit report
     *
     * @return View
     */
    public function creditReport() :View{


        return view('user.report.credit_report',[

            'meta_data'       => $this->metaData(['title'=> translate("Credit Reports")]),
            "reports"         => CreditLog::with(['user'])
                                    ->where('user_id',$this->user->id)
                                    ->search(['remarks','trx_code'])
                                    ->filter(['type'])
                                    ->date()
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all()),



        ]);

    }





    /**
     * Credit report
     *
     * @return View
     */
    public function kycReport() :View{


        return view('user.report.kyc_report',[

            'meta_data'       => $this->metaData(['title'=> translate("KYC Reports")]),
            "reports"         => KycLog::with(['user','file'])
                                    ->where('user_id',$this->user->id)
                                    ->search(['notes'])
                                    ->filter(['status'])
                                    ->date()
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all()),

        ]);

    }


    /**
     * webhook report
     *
     * @return View|RedirectResponse
     */
    public function webhookReport() :View | RedirectResponse{

        if($this->webhookAccess == StatusEnum::true->status()){
            return view('user.report.webhook_report',[
                'meta_data'       => $this->metaData(['title'=> translate("Webhook Reports")]),
                "reports"         => PostWebhookLog::where('user_id',$this->user->id)
                    ->date()
                    ->latest()
                    ->paginate(paginateNumber())
                    ->appends(request()->all()),

            ]);
        }

        return redirect()->route('user.home')->with('error',translate('You current plan doesnot have webhook access'));

    }
}
