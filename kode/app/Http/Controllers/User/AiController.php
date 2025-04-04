<?php

namespace App\Http\Controllers\User;

use App\Enums\PlanDuration;
use App\Http\Controllers\Controller;
use App\Enums\StatusEnum;
use App\Http\Requests\ContentRequest;
use App\Http\Services\AiService;
use App\Http\Services\ContentService;
use App\Models\Admin\Category;
use App\Models\Admin\Template;
use App\Models\AiTemplate;
use App\Models\Content;
use Illuminate\Http\Request;
use App\Traits\ModelAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
class AiController extends Controller
{

    use ModelAction ;
    protected  $user,$contentService , $templates ,$aiService ,$remainingToken ;

    public function __construct(){

        $this->contentService  = new ContentService();
        $this->aiService       =  new AiService();
        $this->middleware(function ($request, $next) {

            $this->user             = auth_user('web');
            $subscription           = $this->user->runningSubscription;
            $templateAccess         = $subscription ? (array)subscription_value($subscription,"template_access",true) :[];
            $this->templates        = AiTemplate::whereIn('id',$templateAccess)->get();
            $this->remainingToken   = $subscription ? $subscription->remaining_word_balance : 0;

            return $next($request);
        });
    }

    /**
     * Content list
     *
     * @return View
     */
    public function list() :View{


        $accessCategories = (array)@$this->templates->pluck('category_id')->unique()->toArray();

        return view('user.content.list',[

            'meta_data'    => $this->metaData(['title'=> translate("AI Contents")]),

            'contents'     => Content::where('user_id',$this->user->id)
                                        ->search(['name'])
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all()),

            'categories'  => Category::template()
                                        ->doesntHave('parent')
                                        ->whereIn('id',$accessCategories)
                                        ->get(),

            'templates'  =>     $this->templates

        ]);
    }


    /**
     * Update a specific Article
     *
     * @param ContentRequest $request
     * @return RedirectResponse
     */
    public function store(ContentRequest $request) :RedirectResponse {

        $content            =  new Content();
        $content->name      =  $request->input('name');
        $content->user_id   =  $this->user->id;
        $content->content   =  $request->input('content');
        $content->save();

        return  back()->with(response_status('Content created successfully'));
    }



    /**
     * Update a specific Article
     *
     * @param ContentRequest $request
     * @return RedirectResponse
     */
    public function update(ContentRequest $request) :RedirectResponse {

        $content = Content::where('user_id',$this->user->id)
                       ->where("id",$request->input('id'))->firstOrfail();

        return  back()->with($this->contentService->update($request , $content));
    }



    /**
     * Update a specific Article status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:contents,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"      => new Content(),
            "user_id"    => $this->user->id,
        ]);
    }


    public function destroy(string | int $id) :RedirectResponse{

        $content  = Content::where('user_id',$this->user->id)->where('id',$id)->firstOrfail();
        $content->delete();
        return  back()->with(response_status('Item deleted succesfully'));
    }


    public function generate(Request $request): string
    {
        try {
            $templateRules   =  $this->aiService->setRules($request);
            $request->validate(Arr::get($templateRules, 'rules', []),Arr::get($templateRules, 'messages', []));

            $response ['status']  =  false;
            $response ['message'] =  translate("Insufficient word tokens to utilize the template. Please acquire additional tokens for access");

            if($request->input('custom_prompt') == StatusEnum::false->status()){
                $template        = Arr::get($templateRules,'template');
                $accessTemplates = $this->templates ? @$this->templates->pluck('id')->toArray() :[];
                if(!in_array(@$template->id, $accessTemplates)) {
                    return json_encode([
                        "status"       => false,
                        "message"      => translate("AI template access unavailable. Ensure an active subscription for utilization. Thank you for your understanding"),
                    ]);
                }
            }

            if($this->remainingToken == PlanDuration::UNLIMITED->value || $this->remainingToken > (int) $request->input('max_result') ){
                $request->validate(Arr::get($templateRules, 'rules', []));
                $response =  $request->input('custom_prompt') == StatusEnum::false->status()
                                        ? $this->aiService->generatreContent($request,$templateRules['template'])
                                        : $this->aiService->generatreCustomPromptContent($request) ;
            }



            return json_encode($response);

        } catch (\Exception $ex) {
            return json_encode([
                "status"       => false,
                "message"      => $ex->getMessage()
            ]);
        }
    }

}
