<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\StatusEnum;
use App\Http\Requests\Admin\LogoSettingRequest;
use App\Http\Requests\Admin\CustomInputRequest;
use App\Http\Services\SettingService;
use App\Models\Core\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
class SettingController extends Controller
{

    protected $settingService;

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_settings'])->only(['list','systemConfiguration']);
        $this->middleware(['permissions:update_settings'])->only(['pluginSetting','systemInfo','logoSetting','store','webhook','kycConfig']);

        $this->settingService = new SettingService();
    }

    /**
     * get all system settings
     * @return View
     */
    public function list() :View
    {
        return view('admin.setting.list',[
            'title'       => 'Settings',
            'breadcrumbs' => ['home'=>'admin.home','Settings'=> null],
            'timeZones'   => timezone_identifiers_list(),
            'countries'   => get_countries()
        ]);
    }

    /**
     * get all system systemConfiguration
     * @return View
     */
    public function systemConfiguration() :View
    {
        return view('admin.setting.system_configuration',[
            'title'       => 'System Configuration',
            'breadcrumbs' =>  ['home'=>'admin.home','Configuration'=> null],
        ]);
    }


    /**
     * update plugin settings
     * @return RedirectResponse
     */
    public function pluginSetting(Request $request) :string{

        $status = true;
        $message = translate('Updated Successfully');
        try {
            $this->settingService->updateSettings($request->input('site_settings'));

            if(isset($request->site_settings['google_recaptcha'])){
                if($request->site_settings['google_recaptcha']['status'] == (StatusEnum::true)->status()){
                    Setting::where('key','default_recaptcha')->update(
                        [
                            'value' => (StatusEnum::false)->status()
                        ]
                    );

                }
            }
        }catch (\Exception $exception) {
            $status  = false;
            $message = $exception->getMessage();
        }
        Cache::forget('site_settings');

        return json_encode([
            'status'  => $status,
            'message' => $message
        ]);

    }





     /**
      * update logo settings
      *
      * @param LogoSettingRequest $request
      * @return string
      */
    public function logoSetting(LogoSettingRequest $request) :string{

        $this->settingService->logoSettings($request->except('_token'));

        return json_encode([
            'status'=> true,
            'message'=> translate('Updated Successfully')
        ]);


    }

    /**
     * update  settings
     * @return RedirectResponse
     */
    public function store(Request $request) :string
    {

        $status = true;
        $message = translate('Updated Successfully');
        if($request->site_settings){
            try {
                $validations = $this->settingService->validationRules($request->site_settings);
                $request->validate( $validations['rules'],$validations['message']);
                if(isset($request->site_settings['time_zone'])){
                    $timeLocationFile = config_path('timesetup.php');
                    $time = '<?php $timelog = '.$request->site_settings['time_zone'].' ?>';
                    file_put_contents($timeLocationFile, $time);
                }
                $this->settingService->updateSettings($request->site_settings);

            } catch (\Exception $exception) {
               $status = false;
               $message = $exception->getMessage();
            }
        }


        return json_encode([
            'status' => $status,
            'message' => $message
        ]);

    }



    /**
     * ticket settings
     *
     * @param CustomInputRequest $request
     * @return string
     */
    public function ticketSetting(CustomInputRequest $request) :string{

        $response = $this->settingService->customPrompt($request);
        optimize_clear();
        return json_encode([
            'status'   => Arr::get($response,'status',false),
            'message'  => Arr::get($response,'message',Arr::get(config('server_error'),'server_error',''))
        ]);

    }



    /**
     * clear cache
     * @return RedirectResponse
     */
    public function cacheClear() :RedirectResponse
    {
        optimize_clear();
        return back()->with(response_status('Cache Cleared Successfully'));
    }

    /**
     * clear cache
     * @return View
     */
    public function serverInfo() :View
    {
        $systemInfo = [
            'laravel_version' => app()->version(),
            'server_detail'   => $_SERVER,
        ];
        return view('admin.server_info',[
            'breadcrumbs'     =>  ['home'=>'admin.home','Server Information'=> null],
            'title'           => "Server Information",
            'systemInfo'      =>  $systemInfo
        ]);
    }


    /**
     * update setting status
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request) : JsonResponse{
        $response = $this->settingService->statusUpdate($request);
        return response()->json($response);
    }



    /**
     * ai Configuration
     * @return View
     */
    public function openAi() :View
    {
        return view('admin.setting.open_ai_settings',[
            'breadcrumbs'     =>  ['home'=>'admin.home','Open Ai'=> null],
            'title'           => "AI Configuration",
        ]);
    }


    /**
     * get webhook settings
     *
     * @return View
     */
    public function webhook() :View
    {
        return view('admin.setting.webhook',[
            'title'       => 'Webhook Settings',
            'breadcrumbs' => ['home'=>'admin.home','Webhook'=> null],
        ]);
    }

    /**
     * get affiliate settings
     *
     * @return View
     */
    public function affiliate() :View
    {
        return view('admin.setting.affiliate',[
            'title'       => 'Affiliate Settings',
            'breadcrumbs' => ['home'=>'admin.home','Affiliate Settings'=> null],
        ]);
    }

    /**
     * get kyc settings
     * @return View
     */
    public function kycConfig() :View
    {
        return view('admin.setting.kyc_settings',[
            'title'       => 'KYC Configuration',
            'breadcrumbs' => ['home'=>'admin.home','KYC Settings'=> null],
        ]);
    }


    /**
     * KYC settings
     *
     * @param CustomInputRequest $request
     * @return string
     */
    public function kycSetting(CustomInputRequest $request) :string {

        $response = $this->settingService->customPrompt($request,'kyc_settings');
        optimize_clear();
        return json_encode([
            'status'      => Arr::get($response,'status',false),
            'message'     => Arr::get($response,'message',Arr::get(config('server_error'),'server_error',''))
        ]);

    }
}
