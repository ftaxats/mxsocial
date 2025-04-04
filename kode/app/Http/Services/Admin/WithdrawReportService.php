<?php
namespace App\Http\Services\Admin;

use App\Enums\WithdrawStatus;
use App\Models\WithdrawLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WithdrawReportService
{


    /**
     * Get all withdraws with statistics
     *
     * @return array
     */
    public function getReport(): array{


        $graphData = new Collection();

        WithdrawLog::date()
                        ->filter(["user:username", 'method_id', 'status'])
                        ->filter(["user:username",'status'])
                        ->selectRaw("
                                        MONTH(created_at) as month, 
                                        SUM(charge) as charge,
                                        MONTHNAME(created_at) as months,
                                        SUM(base_final_amount) as total,
                                        SUM(CASE WHEN status =  '1'  THEN base_final_amount END) AS approved,
                                        SUM(CASE WHEN status =  '2'  THEN base_final_amount END) AS rejected,
                                        SUM(CASE WHEN status =  '3'  THEN base_final_amount END) AS pending
                                      
                                    ")

                        ->groupBy('month', 'months')
                        ->orderBy('month')
                        ->chunk(1000, function (Collection $logs) use (&$graphData) : void {
                            $graphData  = $logs->map(fn(WithdrawLog $log): array =>
                                    [$log->months =>  [
                                        'total'    => $log->total ?? 0,
                                        'charge'   => $log->charge ?? 0,
                                        'rejected' => $log->rejected ?? 0,
                                        'approved' => $log->approved ?? 0,
                                        'pending'  => $log->pending ?? 0,
                                    ]]);
                        });



        return [

            'breadcrumbs'     =>  ['Home'=>'admin.home','Withdraw Report'=> null],
            'title'           => 'Withdraw Reports',
            "reports"         =>  WithdrawLog::with(['user','method','currency'])
                                            ->search(['trx_code'])
                                            ->filter(["user:username",'status'])
                                            ->date()               
                                            ->latest()
                                            ->paginate(paginateNumber())
                                            ->appends(request()->all()),
                                            
            'total_withdraw'  =>  num_format(number:WithdrawLog::filter(["user:username",'status'])
                                                    ->sum('base_final_amount'),calC :true),

            'summaries'       => [

                'total_income_by_charge'  => num_format(number:WithdrawLog::approved()
                                                            ->filter(["user:username",'status'])
                                                            ->sum('charge'),calC :true),
                'success_withdraw'         => num_format(number:WithdrawLog::approved()
                                                            ->filter(["user:username",'status'])
                                                            ->sum('base_final_amount'),calC :true),
                                                                    
                
                'this_year'               => num_format(number:WithdrawLog::approved()
                                                                ->filter(["user:username",'status'])
                                                                ->whereYear('created_at', '=',date("Y"))
                                                                ->sum('base_final_amount'),calC :true),

                'this_month'              => num_format(number:WithdrawLog::approved()
                                                                    ->filter(["user:username",'status'])
                                                                    ->whereMonth('created_at', '=',date("m"))
                                                                    ->sum('base_final_amount'),calC :true),

                'this_week'               => num_format(number:WithdrawLog::approved()->filter(["user:username",'status'])
                                                                ->whereBetween('created_at', 
                                                                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                                                ->sum('base_final_amount'),calC :true),


                'today'                   => num_format(number:WithdrawLog::approved()
                                                                    ->filter(["user:username",'status'])
                                                                    ->whereDate('created_at', Carbon::today())
                                                                    ->sum('base_final_amount'),calC :true),
            ],
  
           'graph_data'       => sortByMonth($graphData->collapse()->all(),true,
                                                            [
                                                             'total'     => 0,
                                                             'charge'    => 0,
                                                             'approved'  => 0,
                                                             'rejected'  => 0,
                                                             'pending'   => 0,
                                                            ]), 
                            

            ];
     

    }



    /**
     * Get specific withdraw report
     *
     * @param integer|string $id
     * @param WithdrawStatus|null $status
     * @return WithdrawLog|null
     */
    public function getSpecificReport(int|string $id , ? WithdrawStatus $status = null): ?WithdrawLog{
        return  WithdrawLog::with(['user','method','method','currency',"file"])
                                  ->when($status , fn(Builder $q): Builder => $q->where("status",(string)$status->value))
                                  ->findOrfail($id);
    } 






}
