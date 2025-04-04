<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\StatusEnum;
use App\Http\Requests\CurrencyRequest;
use App\Http\Services\SettingService;
use App\Models\Admin\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Traits\ModelAction;
use Illuminate\Support\Arr;

class CurrencyController extends Controller
{
    use ModelAction;
 
    public function __construct(){

        $this->middleware(['permissions:view_currency'])->only('list');
        $this->middleware(['permissions:update_currency'])->only('update','updateStatus','setDefault','currencyConfig','bulk');
        $this->middleware(['permissions:delete_currency'])->only('destroy');
    }

    public function list() :View{

        return view('admin.currency.list', [
            'title'       => 'Currency List',
            'breadcrumbs' => ['Dashboard' => 'admin.home', 'Currencies' => null],
            'currencies'  => Currency::search(['name','code'])->with(['createdBy', 'updatedBy'])
                                ->latest()
                                ->paginate(paginateNumber())
                                ->appends(request()->all())
        ]);
    }




    public function store(CurrencyRequest $request) :RedirectResponse{

        $currency                  = new Currency();
        $currency->name            = $request->input('name');
        $currency->symbol          = $request->input('symbol');
        $currency->code            = $request->input('code');
        $currency->exchange_rate   = $request->input('exchange_rate');
        $currency->save();

        return back()->with(response_status('Currency created successfully'));
    }

   

    public function update(CurrencyRequest $request) :RedirectResponse{

        $currency                  = Currency::where('id', $request->input('id'))->firstOrfail();
        $currency->name            = $request->input('name');
        $currency->symbol          = $request->input('symbol');
        $currency->code            = $request->input('code');
        $currency->exchange_rate   = $request->input('exchange_rate');
        $currency->save();
        return back()->with(response_status('Currency updated successfully'));
    }


    public function currencyConfig(Request $request) :RedirectResponse{

        $request->validate([
            "site_settings"                      => ["required","array"],
            "site_settings.currency_alignment"   => ["required",Rule::in(Arr::get(config('settings'),'currency_alignment' ,[]))],
            "site_settings.num_of_decimal"       => ["required",'numeric','min:0',"max:5"],
            "site_settings.decimal_separator"    => ["required","max:2"],
            "site_settings.thousands_separator"  => ["required","max:2"],
            "site_settings.price_format"         => ["required",Rule::in(Arr::get(config('settings'),'price_format' ,[]))],
            "site_settings.truncate_after"       => ["nullable","min:999",'numeric'],
        ]);

        (new SettingService())->updateSettings($request->site_settings);
        return back()->with(response_status('Updated successfully'));

    }


    public function setDefault(string $uid) :RedirectResponse{

        Currency::where('uid','!=',$uid)->update([
          'default' => (StatusEnum::false)->status(),
        ]);

        Currency::where('uid',$uid)->update([
          'default'=>(StatusEnum::true)->status(),
        ]);

        return back()->with(response_status("Default currency set successfully"));

    }

 
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:currencies,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Currency(),
        ]);
    }

 
    public function destroy(string | int $id) :RedirectResponse{

        $currency = Currency::withCount(['gateway','withdraws','transactions','deposits'])
                        ->regular()
                        ->when(session('currency') , function($q){
                            return $q->where("code","!=",session('currency')->code);
                        })
                        ->where('id', $id)->firstOrFail();
        $response =  response_status('Can not be deleted!! item has related data','error');

        if(1  > $currency->gateway_count && 1  > $currency->withdraws_count && 1  > $currency->transactions_count && 1  > $currency->deposits_count ){
            $response =  response_status('Currency deleted');
            $currency->delete();
        }

        return back()->with($response);
    }
   
    /**
     * Bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulk(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new Currency(),
                "with_count"   => ['gateway','withdraws','transactions','deposits'],
            ]);
    
        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }


}
