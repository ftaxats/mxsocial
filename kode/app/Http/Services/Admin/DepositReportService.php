<?php
namespace App\Http\Services\Admin;

use App\Enums\DepositStatus;
use App\Models\Admin\PaymentMethod;
use App\Models\PaymentLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
class DepositReportService
{


    /**
     * Get all deposit log  statistics
     *
     * @return array
     */
    public function getReport(): array{


        $graphData = new Collection();

        PaymentLog::date()
            ->filter(["user:username", 'method_id', 'status'])
            ->whereYear('created_at',  date('Y'))
            ->filter(["user:username", 'package:slug'])
            ->selectRaw("MONTH(created_at) as month, 
                            MONTHNAME(created_at) as months,
                            SUM(base_final_amount) as total,
                            SUM(charge) as charge,
                            SUM(CASE WHEN status = '-1'  THEN base_final_amount END) AS initiate,
                            SUM(CASE WHEN status =  '1'  THEN base_final_amount END) AS paid,
                            SUM(CASE WHEN status =  '2'  THEN base_final_amount END) AS cancel,
                            SUM(CASE WHEN status =  '3'  THEN base_final_amount END) AS pending,
                            SUM(CASE WHEN status =  '5'  THEN base_final_amount END) AS rejected")

            ->groupBy('month', 'months')
            ->orderBy('month')
            ->chunk(1000, function (Collection $logs) use (&$graphData) : void {
                $graphData  = $logs->map(fn(PaymentLog $log): array =>
                        [$log->months =>  [
                            'total'    => $log->total ?? 0,
                            'charge'   => $log->charge ?? 0,
                            'initiate' => $log->initiate ?? 0,
                            'success'  => $log->paid ?? 0,
                            'cancel'   => $log->cancel ?? 0,
                            'pending'  => $log->pending ?? 0,
                            'rejected' => $log->rejected ?? 0,
                        ]]
                );
            });

        

            return [

                'breadcrumbs'     =>  ['Home'=>'admin.home','Deposit Reports'=> null],
                'title'           => 'Deposit Reports',
                "reports"         =>  PaymentLog::with(['user','method','method.currency','currency'])
                                            ->search(['trx_code'])
                                            ->filter(["user:username",'method_id','status'])
                                            ->date()               
                                            ->latest()
                                            ->paginate(paginateNumber())
                                            ->appends(request()->all()),
                                            
                'methods'            => PaymentMethod::active()->get(),
    
                'total_deposit'      =>  num_format(number:PaymentLog::filter(["user:username",'method_id','status'])
                                                                    ->sum('base_final_amount'),calC :true),
    
    
                
                'summaries'      => [
    
                        'total_income_by_charge'  => num_format(number:PaymentLog::paid()
                                                                    ->filter(["user:username",'method_id','status'])
                                                                    ->sum('charge'),calC :true),
                        'success_deposit'         => num_format(number:PaymentLog::paid()
                                                                            ->filter(["user:username",'method_id','status'])
                                                                            ->sum('base_final_amount'),calC :true),
                                                                            
                        
                        'this_year'               => num_format(number:PaymentLog::paid()
                                                                            ->filter(["user:username",'method_id','status'])
                                                                            ->whereYear('created_at', '=',date("Y"))
                                                                            ->sum('base_final_amount'),calC :true),
    
                        'this_month'              => num_format(number:PaymentLog::paid()
                                                                            ->filter(["user:username",'method_id','status'])
                                                                            ->whereMonth('created_at', '=',date("M"))
                                                                            ->sum('base_final_amount'),calC :true),
    
                        'this_week'               => num_format(number:PaymentLog::paid()->filter(["user:username",'method_id','status'])
                                                                            ->whereBetween('created_at', 
                                                                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                                                            ->sum('base_final_amount'),calC :true),
    
    
                        'today'                   => num_format(number:PaymentLog::paid()
                                                                            ->filter(["user:username",'method_id','status'])
                                                                            ->whereDate('created_at', Carbon::today())
                                                                            ->sum('base_final_amount'),calC :true),
                ],
    
                'graph_data'       => sortByMonth(@$graphData->collapse()->all() ?? [],true,['total'   => 0,
                                                                    'initiate' => 0,
                                                                    'success'  => 0,
                                                                    'cancel'   => 0,
                                                                    'pending'  => 0,
                                                                    'rejected' => 0]), 
             
            ];
    
    }


    /**
     * Get specific deposit report
     *
     * @param integer|string $id
     * @param DepositStatus|null $status
     * @return PaymentLog|null
     */
    public function getSpecificReport(int|string $id , ? DepositStatus $status = null): ?PaymentLog{
        return  PaymentLog::with(['user','method','method.currency','currency','file'])
                                            ->when($status , fn(Builder $q): Builder => $q->where("status",(string)$status->value))
                                            ->findOrfail($id);
    }






}
