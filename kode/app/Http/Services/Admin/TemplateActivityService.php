<?php
namespace App\Http\Services\Admin;

use App\Models\AiTemplate;
use App\Models\TemplateUsage;
use Carbon\Carbon;

class TemplateActivityService
{


    /**
     * Get all template useages  statistics
     *
     * @return array
     */
    public function getReport(): array{


        $templates = AiTemplate::whereHas("templateUsages")->get();


        return [
            
            'breadcrumbs' =>  ['Home'=>'admin.home','Templates Reports'=> null],
            'title' => 'Templates Reports',
            "reports"         => TemplateUsage::with(['template','admin','user'])
                                    ->filter(['template:slug',"user:username"])
                                    ->date()               
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all()),
                                    
            "templates"       => $templates ,

            'summaries'       => [
                                    'total_words' => truncate_price(TemplateUsage::filter(['template:slug',"user:username"])
                                                                                    ->sum("total_words")),
                                    
                                    'this_year'   => truncate_price(TemplateUsage::filter(['template:slug',"user:username"])
                                                                        ->whereYear('created_at', '=',date("Y"))
                                                                        ->sum("total_words"),1),

                                    'this_month'  => truncate_price(TemplateUsage::filter(['template:slug',"user:username"])
                                                                        ->whereMonth('created_at', '=',date("M"))
                                                                        ->sum("total_words"),0),

                                    'this_week'  => truncate_price(TemplateUsage::filter(['template:slug',"user:username"])
                                                                                    ->whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                                                                                    ->sum("total_words"),0),
                                    'today'       => truncate_price(TemplateUsage::filter(['template:slug',"user:username"])
                                                        ->whereDate('created_at', Carbon::today())
                                                        ->sum("total_words"),0),
                                                        
                                    'total_template_usages' => $templates->count(),
                        
             ],

            'graph_data'       => sortByMonth(TemplateUsage::filter(['template:slug',"user:username"])
                                                    ->selectRaw("MONTHNAME(created_at) as months,  sum(total_words) as total")
                                                    ->whereYear('created_at', '=',date("Y"))
                                                    ->groupBy('months')
                                                    ->pluck('total', 'months')
                                                    ->toArray())
           
         
        ];
        
    
    }


    /**
     * Destroy a specific template report
     *
     * @param integer|string $id
     * @return boolean
     */
    public function destroy(int|string $id) : bool {
        $report  = TemplateUsage::where('id',$id)->firstOrfail();
        $report->delete();
        return true;
    }






}
