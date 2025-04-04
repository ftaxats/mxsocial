<?php
namespace App\Http\Services\Admin;

use App\Models\AffiliateLog;
use App\Models\CreditLog;
use Carbon\Carbon;

class AffiliateService
{


    /**
     * Get all affilite log  statistics
     *
     * @return array
     */
    public function getReport(): array{

        return [
            'breadcrumbs'     =>  ['Home'=>'admin.home','Affiliate Reports'=> null],
            'title'           => 'Affiliate Reports',
            "reports"         =>  AffiliateLog::with(['user','subscription','subscription.package','referral'])
                                                    ->search(['trx_code'])
                                                    ->filter(["user:username"])
                                                    ->date()               
                                                    ->latest()
                                                    ->paginate(paginateNumber())
                                                    ->appends(request()->all()),
            'summaries'       => [
                                    'total_affiliate_earning' => num_format(number:AffiliateLog::filter(["user:username"])
                                                                                ->sum("commission_amount"),calC:true),
                                    
                                    'this_year'               => num_format(number:AffiliateLog::filter(["user:username"])
                                                                                ->whereYear('created_at', '=',date("Y"))
                                                                                ->sum("commission_amount"),calC:true),

                                    'this_month'              => num_format(number:AffiliateLog::filter(["user:username"])
                                                                                ->whereMonth('created_at', '=',date("M"))
                                                                                ->sum("commission_amount"),calC:true),

                                    'this_week'               => num_format(number:AffiliateLog::filter(["user:username"])
                                                                                ->whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                                                                                ->sum("commission_amount"),calC:true),
                                    'today'                    => num_format(number:AffiliateLog::filter(["user:username"])
                                                                                ->whereDate('created_at', Carbon::today())
                                                                                ->sum("commission_amount"),calC:true), 
                ],
                        
                'graph_data'       => sortByMonth(AffiliateLog::filter(['template:slug',"user:username"])
                                                        ->selectRaw("MONTHNAME(created_at) as months,  sum(commission_amount) as total")
                                                        ->whereYear('created_at', '=',date("Y"))
                                                        ->groupBy('months')
                                                        ->pluck('total', 'months')
                                                        ->toArray(),true)
         
            ];
    
    }

}
