<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileRequest;

use App\Models\Admin\Category;

use App\Models\Admin\PaymentMethod;
use App\Models\Admin\Withdraw;
use App\Models\AiTemplate;
use App\Models\Blog;
use App\Models\Core\File;
use App\Models\CreditLog;
use App\Models\Link;
use App\Models\MediaPlatform;
use App\Models\Notification;
use App\Models\Package;
use App\Models\PaymentLog;
use App\Models\SocialAccount;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Visitor;
use App\Models\WithdrawLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Traits\Fileable;
use Barryvdh\Debugbar\Twig\Extension\Debug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{


    use Fileable;

    /**
     * Admin Dashboard
     *
     * @param Request $request
     * @return void
     */
    public function home(Request $request) :View{

        return view('admin.home',[
            'title' => "Dashboard",
            'data'  => $this->getDashboardData()
        ]);
    }



    /**
     * get dashboard data
     *
     */

     public function getDashboardData() :array {


        $data['top_customers']               = User::with(['file'])
                                                        ->withCount(['subscriptions'])
                                                        ->date()
                                                        ->orderBy('subscriptions_count', 'desc')
                                                        ->latest()
                                                        ->take(10)
                                                        ->get();

        $data['latest_log']               = PaymentLog::with(['user','method','method.currency','currency'])
                                                                ->date()
                                                                ->latest()
                                                                ->take(6)
                                                                ->get();
        $data['latest_subscriptions']     = Subscription::with(['package','admin','user'])
                                                                ->date()
                                                                ->latest()
                                                                ->take(8)
                                                                ->get();

        $data['account_repot']            = [

                "total_account"         => SocialAccount::whereNull('user_id')->count(),
                "active_account"        => SocialAccount::whereNull('user_id')->active()->count(),
                "inactive_account"      => SocialAccount::whereNull('user_id')->inactive()->count(),
                "accounts_by_platform"  => MediaPlatform::with('file')->withCount(['accounts'=> fn(Builder $q) :Builder => $q->whereNull("user_id")])
                                                        ->integrated()
                                                        ->pluck('accounts_count','name')
                                                        ->toArray()
        ];

        $subscripIncome = Subscription::date()->whereYear('created_at', '=',date("Y"))->sum('payment_amount');
        $charge         = PaymentLog::paid()->date()->whereYear('created_at','=',date('Y'))->sum("charge");
        $withDrawCharge = WithdrawLog::approved()->date()->whereYear('created_at','=',date('Y'))->sum("charge");


        $data['subscription_reports']    = [

                                    "total_subscriptions"         => Subscription::date()->whereYear('created_at', '=',date("Y"))->count(),
                                    "total_income"                => num_format(
                                                                                number: $subscripIncome,
                                                                                calC:true
                                                                               ),

                                     "monthly_subscriptions"      =>  sortByMonth(Subscription::date()
                                                                            ->selectRaw("MONTHNAME(created_at) as months,  count(*) as total")
                                                                            ->whereYear('created_at', '=',date("Y"))
                                                                            ->groupBy('months')
                                                                            ->pluck('total', 'months')
                                                                            ->toArray()),

                                     "monthly_income"             =>   sortByMonth(Subscription::date()
                                                                            ->selectRaw("MONTHNAME(created_at) as months, SUM(payment_amount) as total")
                                                                            ->whereYear('created_at', '=',date("Y"))
                                                                            ->groupBy('months')
                                                                            ->pluck('total', 'months')
                                                                            ->toArray(),true)

        ];



        $data['total_user']               = User::date()->count();
        $data['total_transaction']        = Transaction::date()->count();
        $data['total_category']           = Category::date()->count();
        $data['total_package']            = Package::date()->count();
        $data['total_visitor']            = Visitor::date()->count();
        $data['total_blog']               = Blog::date()->count();
        $data['total_template']           = AiTemplate::date()->count();
        $data['total_earning']            = num_format(
                                                number: $subscripIncome + $charge + $withDrawCharge,
                                                calC:true
                                            ) ;

        $data['total_platform']           = MediaPlatform::active()->count();




        $data['earning_per_months']     = sortByMonth(PaymentLog::paid()->selectRaw("MONTHNAME(created_at) as months, SUM(amount + charge) as total")
                                                        ->whereYear('created_at', '=',date("Y"))
                                                        ->groupBy('months')
                                                        ->pluck('total', 'months')
                                                        ->toArray());

        $data['subscription_by_plan']  =  Package::withCount(['subscriptions'])
                                                            ->pluck('subscriptions_count','title')
                                                            ->toArray();



        $data['withdraw_charge']        = num_format(number:$withDrawCharge,calC:true);
        $data['payment_charge']         = num_format(number:$charge,calC:true);

        $data['monthly_payment_charge']       =  sortByMonth(PaymentLog::date()->paid()->selectRaw("MONTHNAME(created_at) as months, SUM(charge) as total")
                                                                ->whereYear('created_at', '=',date("Y"))
                                                                ->groupBy('months')
                                                                ->pluck('total', 'months')
                                                                ->toArray(),true);



        $data['monthly_withdraw_charge']       =  sortByMonth(WithdrawLog::date()->approved()->selectRaw("MONTHNAME(created_at) as months, SUM(charge) as total")
                                                                ->whereYear('created_at', '=',date("Y"))
                                                                ->groupBy('months')
                                                                ->pluck('total', 'months')
                                                                ->toArray(),true);


        $data['latest_transactiions']           =  Transaction::with(['user','admin','currency'])
                                                                    ->search(['remarks','trx_code'])
                                                                    ->filter(["user:username",'trx_type'])
                                                                    ->date()
                                                                    ->latest()
                                                                    ->take(7)
                                                                    ->get();


        $data['credit_logs']                    =  CreditLog::with(['user','user.file'])
                                                                    ->search(['remark','trx_code'])
                                                                    ->filter(["user:username",'type'])
                                                                    ->date()
                                                                    ->latest()
                                                                    ->take(7)
                                                                    ->get();




        return $data;

     }



    /**
     * Admin profile
     *
     * @return View
     */
    public function profile() :View{

        return view('admin.profile',[

            'breadcrumbs' =>  ['home'=>'admin.home','profile'=> null],
            "user"        =>  auth_user(),
            'title'       => "Profile",
        ]);
    }

    /**
     * @param ProfileRequest $request
     * @return RedirectResponse
     */
    public function profileUpdate(ProfileRequest $request ): RedirectResponse
    {
        $response = response_status('Profile Updated');
        try {

            DB::transaction(function() use ($request) {
                $admin = auth_user();
                $admin->username    = $request->input('username');
                $admin->phone       = $request->input('phone');
                $admin->email       = $request->input('email');
                $admin->name        = $request->input('name');
                $admin->save();


                if($request->hasFile('image')){

                    $oldFile = $admin->file()->where('type','avatar')->first();
                    $response = $this->storeFile(
                        file        : $request->file('image'),
                        location    : config("settings")['file_path']['profile']['admin']['path'],
                        removeFile  : $oldFile
                    );


                    if(isset($response['status'])){
                        $image = new File([
                            'name'      => Arr::get($response, 'name', '#'),
                            'disk'      => Arr::get($response, 'disk', 'local'),
                            'type'      => 'avatar',
                            'size'      => Arr::get($response, 'size', ''),
                            'extension' => Arr::get($response, 'extension', ''),
                        ]);
                        $admin->file()->save($image);
                    }
                }

            });

        } catch (\Exception $ex) {
           $response = response_status(strip_tags($ex->getMessage(),"error"));
        }

        return back()->with($response);
    }


    /**
     * update password
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function passwordUpdate(Request $request ) :RedirectResponse{

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:5',
        ],
        [
            'current_password.required' => translate('Your Current Password is Required'),
            'password' => translate('Password Feild Is Required'),
            'password.confirmed' => translate('Confirm Password Does not Match'),
            'password.min' => translate('Minimum 5 digit or character is required'),
        ]);
        $admin = auth_user();
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', translate("Your Current Password does not match !!"));
        }
        $admin->password = Hash::make($request->password);
        $admin->save();
        return back()->with(response_status('Password Updated'));
    }



    /**
     * read a notifications
     */

     public function readNotification(Request $request) :string{

        $notification = Notification::where('notificationable_type','App\Models\Admin')
            ->where("id", $request->id)
            ->first();
        $status  = false;
        $message = translate('Notification Not Found');
        if( $notification ){
            $notification->is_read =  (StatusEnum::true)->status();
            $notification->save();
            $status = true;
            $message = translate('Notification Readed');
        }
        return json_encode([
            "status"  => $status,
            "message" => $message
        ]);

    }


    /**
     * read a notifications
     */

     public function notification(Request $request) :View{

        Notification::where('notificationable_type','App\Models\Admin')->update([
            "is_read" =>  (StatusEnum::true)->status()
        ]);

        return view('admin.notification',[
            'breadcrumbs'    =>  ['home'=>'admin.home','Notifications'=> null],
            'title'          =>  "Notifications",
            'notifications'  =>  Notification::where('notificationable_type','App\Models\Admin')
                                 ->latest()
                                 ->paginate(paginateNumber())
        ]);
    }



}
