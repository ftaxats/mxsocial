<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\LanguageRequest;
use App\Http\Services\LanguageService;
use App\Models\Core\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use App\Traits\ModelAction;
use Illuminate\View\View;

class LanguageController extends Controller
{
    use ModelAction;
    public $languageService;

    /**
     * Constructs a new instance of the LanguageService class.
     *
     * @return void
     */

    public function __construct()
    {
        $this->languageService = new LanguageService();

        //check permissions middleware
        $this->middleware(['permissions:view_language'])->only('list');
        $this->middleware(['permissions:create_language'])->only('store');
        $this->middleware(['permissions:update_language'])->only(['setDefaultLang','updateStatus']);
        $this->middleware(['permissions:translate_language'])->only(['translate','tranlateKey']);
        $this->middleware(['permissions:delete_language'])->only(['destroyTranslateKey','destroy']);
    }


    /**
     * @return View
     */
    public function list() : View
    {

        return view('admin.language.list', [

            'title'         =>  translate("Manage Language"),
            'breadcrumbs'   =>  ['home'=>'admin.home','language'=> null],
            'languages'     =>  Language::with(['updatedBy','createdBy'])
                                        ->search(['name','code'])
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all()),
            'countryCodes'  =>  json_decode(file_get_contents(resource_path(config('constants.options.country_code')) . 'countries.json'),true)
        ]);
    }

    /**
     * Store a new language.
     *
     * @param LanguageRequest $request
     * @return RedirectResponse
     */
    public function store(LanguageRequest $request): RedirectResponse
    {
        $response = $this->languageService->store($request);
        return back()->with($response['status'],$response['message']);
    }

    /**
     * Make a language as default
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function setDefaultLang(int | string $id) : RedirectResponse
    {

        $response = $this->languageService->setDefault($id);
        return back()->with($response['status'],$response['message']);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $response['reload']  = true;
        $response['status']  = false;
        $response['message'] = translate('Failed To Update');

        try {
            $request->validate([
                'id'      => 'required|exists:languages,uid',
                'status'  => ['required',Rule::in(StatusEnum::toArray())],
                'column'  => ['required',Rule::in(['status'])],
            ]);
            $language = Language::where('uid',$request->input('id'))->firstOrfail();
            $response['status']    = true;
            $response['message']   = translate('Updated Successfully');
            if(session()->get('locale') == $language->code || $language->is_default == (StatusEnum::true)->status()){
                $response['status']  = false;
                $response['message'] = translate('System Current and default language Status Can not be Updated');
            }
            else{
                $language->status = $request->input('status');
                $language->save();
            }
        } catch (\Throwable $th) {
        }

        return json_encode($response);
    }


    /**
     * @param string $code
     * @return View
     */
    public function translate(string $code): View
    {

        return view('admin.language.translate', [
            'title'         =>  translate("Translate language"),
            'breadcrumbs'   =>  ['home'=>'admin.home','language'=> 'admin.language.list' ,"translate"=> null],
            'translations'  =>  $this->languageService->translationVal($code)
        ]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function tranlateKey(Request $request): string
    {

        $response = $this->languageService->translateLang($request);
        return json_encode([
            "success" => $response
        ]);
    }

    /**
     * Destroy A language
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function destroy(int | string $id) : RedirectResponse
    {
        $response = $this->languageService->destory($id);
        return back()->with( $response['status'],$response['message']);
    }



    /**
     * Destroy A language translation
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function destroyTranslateKey(int | string $id) : RedirectResponse
    {
        $response = $this->languageService->destoryKey($id);
        return back()->with( $response['status'],$response['message']);
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
                "model"        => new Language(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }
}
