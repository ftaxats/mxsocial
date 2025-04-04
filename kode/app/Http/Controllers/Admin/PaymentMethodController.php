<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FileKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentMethodRequest;
use App\Models\Admin\Currency;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Traits\ModelAction;
class PaymentMethodController extends Controller
{

    use ModelAction;
    private $currencies;


    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_method'])->only('list');
        $this->middleware(['permissions:create_method'])->only('create','store');
        $this->middleware(['permissions:update_method'])->only(['edit','update','updateStatus','bulk']);
        $this->middleware(['permissions:delete_method'])->only(["destroy",'bulk']);

        $this->currencies =  Currency::active()->get();

    }


    /**
     * payment method list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.payment_method.list',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Payment Method'=> null],
            'title'       => 'Manage Payment Methods',
            'methods'     => PaymentMethod::with(['createdBy','updatedBy','currency','file'])
                                        ->type()
                                        ->search(['name'])
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all())
        ]);
    }

    /**
     * create payment method
     *
     * @return View
     */
    public function create() :View{

        return view('admin.payment_method.create',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Methods'=> route("admin.paymentMethod.list",['type' => request()->route('type')]) , "Create" => null],
            'title'       =>  'Create Payment Methods',
            "currencies"  =>  $this->currencies
        ]);
    }


    /**
     * update payment methods
     *
     * @return View
     */
    public function edit() :View{

        $method = PaymentMethod::with(['file','currency'])->type()->where('uid',request()->route('uid'))->firstOrFail();
        return view('admin.payment_method.edit',[

            'breadcrumbs' => ['Home'=>'admin.home','Methods'=> route("admin.paymentMethod.list",['type' => request()->route('type')]) ,"Edit"=>null],
            'title'       => 'Update '.ucfirst($method->name),
            'method'      => $method,
            "currencies"  => $this->currencies
        ]);
    }




    /**
     * update payment method
     *
     * @param PaymentMethodRequest $request
     * @return RedirectResponse
     */
    public function store(PaymentMethodRequest $request) :RedirectResponse{


        DB::transaction(function() use ($request) {

            $method                     = new PaymentMethod();
            $method->serial_id          = $request->input("serial_id");
            $method->currency_id        = $request->input("currency_id");
            $method->percentage_charge  = $request->input("percentage_charge");
            $method->fixed_charge       = $request->input("fixed_charge");
            $method->minimum_amount     = $request->input('minimum_amount');
            $method->maximum_amount     = $request->input('maximum_amount');
            $method->note               = $request->input("note");
            $method->setParameters();
            $method->save();

            if($request->hasFile('image')){
                $this->saveFile($method ,$this->storeFile(
                    file        : $request->file('image'),
                    location    : config("settings")['file_path']['payment_method']['path'])
                    ,FileKey::FEATURE->value);
            }

        });

        return  back()->with(response_status(trans('default.created_successfully')));
    }

    /**
     * update payment method
     *
     * @param PaymentMethodRequest $request
     * @return RedirectResponse
     */
    public function update(PaymentMethodRequest $request) :RedirectResponse
    {
        DB::transaction(function() use ($request) {

            $method                     = PaymentMethod::with(['file'])->type()->where('id',$request->input('id'))->firstOrfail();
            $method->name               = $request->input("name");
            $method->serial_id          = $request->input("serial_id");
            $method->currency_id        = $request->input("currency_id");
            $method->percentage_charge  = $request->input("percentage_charge");
            $method->fixed_charge       = $request->input("fixed_charge");
            $method->note               = $request->input("note");
            $method->minimum_amount     = $request->input('minimum_amount');
            $method->maximum_amount     = $request->input('maximum_amount');
            $method->setParameters();
            $method->save();

            if($request->hasFile('image')){
                $oldFile = $method->file()->where('type',FileKey::FEATURE->value)->first();
                $this->saveFile($method ,$this->storeFile(
                    file        : $request->file('image'),
                    location    : config("settings")['file_path']['payment_method']['path'],
                    removeFile  : $oldFile
                    )
                    ,FileKey::FEATURE->value);

            }
        });

        return  back()->with(response_status(trans('default.updated_successfully')));
    }

    /**
     * Update a specific Payment Method status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:payment_methods,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new PaymentMethod(),
        ]);

    }


    /**
     * destroy a specific payment method
     *
     * @param string|int $id
     * @return RedirectResponse
     */
    public function destroy(string |int $id) :RedirectResponse{

        $method  =  PaymentMethod::with('file')
                                ->manual()
                                ->withCount(['deposits'])
                                ->where('id',$id)
                                ->firstOrFail();

       $response =  response_status('Can not be deleted!! item has related data','error');

        if(1  > $method->deposits_count ){

            $oldFile = $method->file()->where('type',FileKey::FEATURE->value)->first();
            $this->unlink(
                location    : config("settings")['file_path']['payment_method']['path'],
                file        : $oldFile
            );

            $response =  response_status('Payment method deleted');
            $method->delete();
        }

        return  back()->with($response);
    }



    public function bulk(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new PaymentMethod(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }
}
