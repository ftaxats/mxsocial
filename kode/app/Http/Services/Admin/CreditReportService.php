<?php
namespace App\Http\Services\Admin;

use App\Models\CreditLog;
use Carbon\Carbon;

class CreditReportService
{


    /**
     * Get all credit log  statistics
     *
     * @return array
     */
    public function getReport(): array{


        return [

            'breadcrumbs'     => ['Home'=>'admin.home','Credit Reports'=> null],
            'title'           => 'Credit Reports',
            "reports"         => CreditLog::with(['user'])
                                    ->search(['remarks','trx_code'])
                                    ->filter(["user:username",'type'])
                                    ->date()               
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all()),


            
            'summaries'       => [
                                        'total_log'    => (CreditLog::filter(["user:username",'type'])->count()),
                                        
                                        'this_year'    => truncate_price(CreditLog::whereYear('created_at', '=',date("Y"))
                                                                            ->filter(["user:username",'type'])
                                                                            ->count(),0),
                                        'this_month'   => truncate_price(CreditLog::whereMonth('created_at', '=',date("M"))
                                                                            ->filter(["user:username",'type'])
                                                                            ->count(),0),
                                        'this_week'    => truncate_price(CreditLog::whereBetween('created_at', 
                                        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                                                        ->filter(["user:username",'type'])
                                                                        ->count(),0),
                                        'today'        => truncate_price(CreditLog::whereDate('created_at', Carbon::today())
                                                                            ->filter(["user:username",'type'])
                                                                            ->count(),0),
            ],
    
            'graph_data'       => sortByMonth(CreditLog::selectRaw("MONTHNAME(created_at) as months,  count(*) as total")
                                                                ->filter(["user:username",'type'])
                                                                ->whereYear('created_at', '=',date("Y"))
                                                                ->groupBy('months')
                                                                ->pluck('total', 'months')
                                                                ->toArray())


         
        ];
        
    
    }


    /**
     * Destroy a specific credit report
     *
     * @param integer|string $id
     * @return boolean
     */
    public function destroy(int|string $id) : bool {
        $report = CreditLog::findOrfail($id);
        $report->delete();
        return true;
     
    }






}
