<?php
namespace App\Http\Services\Admin;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TransactionService
{


    /**
     * Get all transaction with statistics
     *
     * @return array
     */
    public function getReport(): array{
        
        $graphData = new Collection();

        Transaction::with(['currency'])
            ->date()
            ->filter(["user:username"])
            ->whereYear('created_at',  date('Y'))
            ->selectRaw("MONTH(created_at) as month, 
                            MONTHNAME(created_at) as months,
                            SUM(final_amount) as total,
                            SUM(charge) as charge,
                            SUM(CASE WHEN trx_type ='+'  THEN amount END) AS plus,
                            SUM(CASE WHEN trx_type ='-'  THEN amount END) AS minus"
                        )
            ->groupBy('month', 'months')
            ->orderBy('month')
            ->chunk(1000, function (Collection $logs) use (&$graphData) : void {
                $graphData  = $logs->map(fn(Transaction $log) : array =>
                        [$log->months =>  [
                            'total'    => convert_to_base(amount : $log->total ?? 0 ,currency:$log->currency ),
                            'charge'   => convert_to_base(amount : $log->charge ?? 0 ,currency:$log->currency ),
                            'plus'     => convert_to_base(amount : $log->plus ?? 0 ,currency:$log->currency ),
                            'minus'    => convert_to_base(amount : $log->minus ?? 0 ,currency:$log->currency ),
                        ]]
                );
            });

           return  [

                        'breadcrumbs'     =>  ['Home'=>'admin.home','Transaction Reports'=> null],
                        'title'           =>  'Transaction Reports',
                        "reports"         =>  Transaction::with(['user','admin','currency'])
                                                            ->search(['remarks','trx_code'])
                                                            ->filter(["user:username",'trx_type'])
                                                            ->date()               
                                                            ->latest()
                                                            ->paginate(paginateNumber())
                                                            ->appends(request()->all()),
                        'summaries'       => [
            
                                'total_transaction'       => Transaction::filter(["user:username"])
                                                                ->date()
                                                                ->count(),
                                
                                'positive_transaction'    => Transaction::where('trx_type',Transaction::$PLUS)
                                                                ->whereYear('created_at', '=',date("Y"))
                                                                ->filter(["user:username"])
                                                                ->date()
                                                                ->count(),
                                'negative_transaction'    => Transaction::where('trx_type',Transaction::$MINUS)
                                                                ->whereMonth('created_at', '=',date("Y"))
                                                                ->filter(["user:username"])
                                                                ->date()
                                                                ->count(),
            
                                'today'                   => Transaction::whereDate('created_at', Carbon::today())
                                                                ->filter(["user:username"])
                                                                ->count(),
                        ],
    
        
                      'graph_data'       => sortByMonth(@$graphData->collapse()->all() ?? [],true,
                                                           [
                                                                'total'    => 0,
                                                                'charge'   => 0,
                                                                'success'  => 0,
                                                                'plus'     => 0,
                                                                'minus'    => 0
                                                            ]), 
    
                ];

    }


    /**
     * Destroy a specific transactions
     *
     * @param integer|string $id
     * @return boolean
     */
    public function destroy(int|string $id) : bool {
        $report = Transaction::findOrfail($id);
        $report->delete();
        return true;
    }






}
