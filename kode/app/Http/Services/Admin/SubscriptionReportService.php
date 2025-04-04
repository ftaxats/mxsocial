<?php
namespace App\Http\Services\Admin;

use App\Enums\SubscriptionStatus;
use App\Http\Utility\SendNotification;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\CreditLog;
use App\Models\Package;
use App\Models\Subscription;
use App\Traits\Notifyable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SubscriptionReportService
{

    use Notifyable;

    /**
     * Get all subscription log  statistics
     *
     * @return array
     */
    public function getReport(): array{

        return [
            'breadcrumbs'     =>  ['Home'=>'admin.home','Subscription Reports'=> null],
            'title'           => 'Subscription Reports',
            "reports"         =>  Subscription::with(['user','package','oldPackage'])
                                        ->search(['trx_code'])
                                        ->filter(["user:username",'package:slug'])
                                        ->date()               
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all()),

            "packages"                        => Package::all(),
            "total_subscription_amount"       => num_format(number: Subscription::filter(["user:username",'package:slug'])
                                                                               ->sum('payment_amount'),calC:true),

            'summaries'      => [

                            'total_subscription'    => truncate_price(Subscription::filter(["user:username",'package:slug'])->count(),0),
                            
                            'this_year'             => truncate_price(Subscription::filter(["user:username",'package:slug'])
                                                                ->whereYear('created_at', '=',date("Y"))
                                                                ->count(),0),

                            'this_month'            => truncate_price(Subscription::filter(["user:username",'package:slug'])
                                                                ->whereMonth('created_at', '=',date("M"))
                                                                ->count(),0),

                            'this_week'             => truncate_price(Subscription::filter(["user:username",'package:slug'])
                                                                ->whereBetween('created_at', 
                                                                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                                                ->count(),0),
    

                            'today'                => truncate_price(Subscription::filter(["user:username",'package:slug'])
                                                                ->whereDate('created_at', Carbon::today())
                                                                ->count(),0)
            ],

            'graph_data'       => sortByMonth(Subscription::date()
                                                ->filter(["user:username",'package:slug'])
                                                ->selectRaw("MONTHNAME(created_at) as months, SUM(payment_amount) as total")
                                                ->whereYear('created_at', '=',date("Y"))
                                                ->groupBy('months')
                                                ->pluck('total', 'months')
                                                ->toArray(),true),
                                                
        ];

    }


  



    /**
     * Update a specific subscription details 
     *
     * @param Request $request
     * @return array
     */
    public function updateSubscription(Request $request): array{
        
        $subscription             = Subscription::with(['user','package'])
                                                    ->where('id',$request->input('id'))
                                                    ->firstOrFail();

        $subscription->status     = $request->input("status");
        $subscription->remarks    = $request->input("remarks");
        $subscription->expired_at = $request->input("expired_at");
        $subscription->save();



        $code = [
            "link"          => route("user.subscription.report.list"), 
            "plan_name"     => $subscription->package->title,
            "time"          => Carbon::now(),
            "status"        => Arr::get(array_flip(SubscriptionStatus::toArray()),$subscription->status ,"Expired")
        ];

        $notifications = [

            'database_notifications' => [
                'action' => [SendNotification::class, 'database_notifications'],
                'params' => [
                    [ $subscription->user, 'SUBSCRIPTION_STATUS', $code, Arr::get( $code , "link", null) ],
                ],
            ],
            'email_notifications' => [
                'action' => [SendMailJob::class, 'dispatch'],
                'params' => [

                    [ $subscription->user, 'SUBSCRIPTION_STATUS', $code],
                ],
            ],
            'sms_notifications' => [
                'action' => [SendSmsJob::class, 'dispatch'],
                'params' => [

                    [ $subscription->user, 'SUBSCRIPTION_STATUS', $code],
                ],
            ],
            
        ];
        $this->notify( $notifications);
        
        return ['status' => true , "message" => translate('Subscription updated')];
        
    }



}
