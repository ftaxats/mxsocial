<?php
namespace App\Http\Services;

use App\Enums\PlanDuration;
use App\Enums\StatusEnum;
use App\Models\AiTemplate;
use App\Models\TemplateUsage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Traits\ModelAction;
use Illuminate\Validation\Rule;
use Orhanerday\OpenAi\OpenAi;
use App\Traits\AccountManager;
class AiService
{

    use  ModelAction ,AccountManager;



    /**
     * store template
     *
     * @param Request $request
     * @return void
     */
    public function saveTemplate(Request $request) :array{

        $response = response_status('Template created successfully');
        try {
            $template                      = new AiTemplate();
            $template->name                = $request->input("name");
            $template->category_id         = $request->input("category_id");
            $template->sub_category_id     = $request->input("sub_category_id");
            $template->description         = $request->input("description");
            $template->icon                = $request->input("icon");
            $template->custom_prompt       = $request->input("custom_prompt");
            $template->is_default          = $request->input("is_default");
            $template->save();

        } catch (\Exception $ex) {
            $response = response_status(strip_tags($ex->getMessage()),'error');
        }

        return  $response;

    }


    /**
     * update template
     *
     * @param Request $request
     * @return void
     */
    public function updateTemplate(Request $request) :array{



        $response = response_status('Template updated successfully');

        try {
            $template                  = AiTemplate::findOrfail($request->input('id'));
            $template->name            = $request->input("name");
            $template->category_id     = $request->input("category_id");
            $template->sub_category_id = $request->input("sub_category_id");
            $template->description     = $request->input("description");
            $template->icon            = $request->input("icon");
            $template->custom_prompt   = $request->input("custom_prompt");
            $template->is_default      = $request->input("is_default") ?? $request->input("is_default");
            $template->prompt_fields   = $this->parseManualParameters();
            $template->save();

        } catch (\Exception $ex) {
            $response = response_status($ex->getMessage(),'error');
        }

        return  $response;
    }


    public function setRules(Request $request) :array{


        $rules = [
            "language"         => ['required'],
            "custom_prompt"    => ['required',Rule::in(StatusEnum::toArray())],

            "max_result"       => [ Rule::requiredIf(function () use ($request) {
                                            return request()->routeIs('user.*');
                                    }),"nullable","numeric",'gt:0','max:5000'],
            "ai_creativity"    => ['nullable',Rule::in(array_values(Arr::get(config('settings'),'default_creativity',[])))],
            "content_tone"     => ['nullable',Rule::in(Arr::get(config('settings'),'ai_default_tone',[]))],
            "custom"           => ['nullable','array']
        ];

        if(request()->input('custom_prompt') == StatusEnum::true->status()){
            $rules['custom_prompt_input'] = ['required'];
        }else{
            $rules['id'] = ['required',"exists:ai_templates,id"];
        }


        $messages = [
            "language.required"         => translate('Please select a input & output language'),
            "id.required"               => translate('Please select a Template'),
            "max_result.required"       => translate('Max result length field is required'),
            "custom_prompt.required"    => translate('Prompt field is required'),
        ];

        if(request()->input('custom_prompt') == StatusEnum::false->status() &&
           request()->input('id')){
            $template = AiTemplate::find($request->input('id'));
            if($template && $template->prompt_fields){
                foreach($template->prompt_fields as $key => $input){
                    if($input->validation == "required"){
                        $rules['custom.'.$key]   = ['required'];
                    }
                }
            }
        }

        return [
            'template'    =>  @$template,
            'rules'       =>  $rules ,
            'messages'    =>  $messages ,
        ];

    }

    public function generatreContent(Request $request ,AiTemplate $template) :array{


        $logData ['template_id'] = $template->id;

        $logData['admin_id']     = request()->routeIs('admin.*')
                                            ? auth_user('admin')?->id
                                            : null;

        $logData['user_id']      = request()->routeIs('user.*')
                                            ? auth_user('web')?->id
                                            : null;

        $customPrompt = $template->custom_prompt;

        if($request->input("custom") &&  $template->prompt_fields){
            foreach($template->prompt_fields as $key => $input){
                $customPrompt = str_replace("{".$key."}",Arr::get($request->input("custom"),$key,"",),$customPrompt);
            }
        }
        $getBadWords     = site_settings('ai_bad_words');

        $processBadWords = $getBadWords
                            ? explode(",",$getBadWords)
                            :[];

        if(is_array($processBadWords)){
            $customPrompt = str_replace($processBadWords,"",$customPrompt);
        }


         $temperature    = (float)($request->input("ai_creativity")
                            ? $request->input("ai_creativity")
                            : site_settings("ai_default_creativity"));
         $aiParams = [
            'model'             => $this->getAiModel(),
            'temperature'       => $temperature,
            'presence_penalty'  => 0.6,
            'frequency_penalty' => 0.2,
        ];

        $aiTone = $request->input("content_tone")
                   ? $request->input("content_tone")
                   : site_settings("ai_default_tone");

        $tokens = (int)($request->input("max_result")
                    ? $request->input("max_result")
                    : site_settings("default_max_result",-1));


        $language = $request->input("language");


        $customPrompt .= "\nPlease provide a concise and relevant response based on the following topic. Ensure the content is focused and informative, with no irrelevant information. The response should be in $language and use a $aiTone tone of voice. Focus on providing clarity and actionable insights. Do not include phrases like 'Of course' ,'certainly' or similar unnecessary introductory statements. If the topic is unclear, inform me without using excessive formalities or politeness.";



        if ($tokens != PlanDuration::UNLIMITED->value) {
            $aiParams['max_tokens'] = $tokens;
        }


        $aiParams['messages'] = [[
            "role"     => "user",
            "content"  => $customPrompt
        ]];

        return  $this->generateContent($aiParams,$logData);

    }







    /**
     * Generate custom prompt content for AI
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function generatreCustomPromptContent(Request $request): array
    {
        $logData = [
            'admin_id'   => request()->routeIs('admin.*') ? auth_user('admin')?->id : null,
            'user_id'    => request()->routeIs('user.*') ? auth_user('web')?->id : null,
            'template_id'=> $request->input('id'),
        ];

        $customPrompt = $request->input('custom_prompt_input') ?? '';
        $badWords     = site_settings('ai_bad_words');
        $processBadWords = $badWords ? explode(',', $badWords) : [];

        if (!empty($processBadWords)) {
            $customPrompt = str_replace($processBadWords, '', $customPrompt);
        }

        $temperature = (float)($request->input('ai_creativity') ?? site_settings('ai_default_creativity', 0.7));
        $aiTone      = $request->input('content_tone') ?? site_settings('ai_default_tone', 'neutral');
        $language    = $request->input('language') ?? 'English';
        $tokens      = (int)($request->input('max_result') ?? site_settings('default_max_result', -1));


        
        $customPrompt .= "\nPlease provide a concise and relevant response based on the following topic. Ensure the content is focused and informative, with no irrelevant information. The response should be in $language language and use a $aiTone tone of voice. Focus on providing clarity and actionable insights. Do not include phrases like 'Of course' , 'certainly' or similar unnecessary introductory statements. If the topic is unclear, inform me without using excessive formalities or politeness.";
     

    
        $aiParams = [
            'model'             => $this->getAiModel(),
            'temperature'       => $temperature,
            'presence_penalty'  => 0.6,
            'frequency_penalty' => 0.0,
            'messages'          => [[
                'role'    => 'user',
                'content' => $customPrompt,
            ]],
        ];

        if ($tokens !== PlanDuration::UNLIMITED->value) {
            $aiParams['max_tokens'] = $tokens;
        }

        return $this->generateContent($aiParams, $logData);
    }



    public function getAiModel() :string | null{

        $model = site_settings("open_ai_model");

        if(request()->routeIs("user.*")){
            $subscription    =  auth_user('web')->runningSubscription;
            $model           = optional(optional($subscription)->package->ai_configuration)->open_ai_model ?? $model;
        }


        return $model ;
    }


    /**
     * Generate content using open ai
     *
     * @param array $aiParams
     * @param array $logData
     * @return array
     */
    public function generateContent(array $aiParams , array $logData) :array {



   
        $status       = false;
        $message      = translate("Invalid Request");
        $open_ai      = new OpenAi(OPENAI_API_KEY: openai_key());
        $chat_results = json_decode($open_ai->chat($aiParams),true);

     
        if(isset($chat_results['error'])){
            $message        = Arr::get($chat_results['error'],'message',translate('Invalid Request'));
        }
        else{

            if(isset($chat_results['choices'][0]['message']['content'])){

                $realContent                   = $chat_results['choices'][0]['message']['content'];
                $content                       = str_replace(["\r\n", "\r", "\n"] ,"<br>",$realContent);
                $content = preg_replace('/^"(.*)"$/', '$1', $content); 
                $usage                         = $chat_results['usage'];

                $usage['model']                = $chat_results['model'];
                $usage['genarated_tokens']     = count(explode(' ', ($content)));



                DB::transaction(function() use ($logData ,$usage ,$content) {

                    $templateId =  Arr::get($logData,'template_id',null);

                    if($templateId){

                        $templateLog                   = new TemplateUsage();
                        $templateLog->user_id          = Arr::get($logData,'user_id',null);
                        $templateLog->admin_id         = Arr::get($logData,'admin_id',null);
                        $templateLog->template_id      = Arr::get($logData,'template_id',null);
                        $templateLog->package_id       = Arr::get($logData,'package_id',null);
                        $templateLog->open_ai_usage    = $usage;
                        $templateLog->content          = $content;
                        $templateLog->total_words      = Arr::get($usage,'genarated_tokens',0);
                        $templateLog->save();
                    }


                    if(request()->routeIs("user.*")){
                        $token = (int) Arr::get($usage , "completion_tokens" ,0);
                        $user  = auth_user('web')->load(['runningSubscription']);

                        $details =  $token ." word generated using custom prompt";

                        if($templateId) $details = $token ." word generated using (".@$templateLog->template->name . ") Template";

                        $this->generateCreditLog(
                            user        : $user,
                            trxType     : Transaction::$MINUS,
                            balance     : (int) $token,
                            postBalance : (int)$user->runningSubscription->remaining_word_balance,
                            details     : $details,
                            remark      : t2k("word_credit"),
                        );

                        $userToken      = @$user->runningSubscription->remaining_word_balance;

                        if(@$userToken != PlanDuration::UNLIMITED->value  && $userToken > 0) {
                            $user->runningSubscription->decrement('remaining_word_balance',$token);
                        }
                    }



                });


                $status         = true;
                $message        = $realContent;
            }


        }

        return [
            "status"      => $status,
            "message"     => $message,
        ];

    }


}