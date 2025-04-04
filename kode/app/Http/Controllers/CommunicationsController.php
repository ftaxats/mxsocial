<?php

namespace App\Http\Controllers;
use App\Enums\StatusEnum;
use App\Http\Requests\ContactRequest;
use App\Http\Utility\SendNotification;
use App\Models\Admin;
use App\Models\Admin\Frontend;
use App\Models\Contact;
use App\Models\Core\File;
use App\Models\Subscriber;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use App\Traits\Notifyable;
use App\Traits\Fileable;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\Admin\Menu;
use Illuminate\Support\Str;

class CommunicationsController extends Controller
{



     use Notifyable , Fileable;

     public $lastSegment;

     public function __construct()
     {
        $this->lastSegment = collect(request()->segments())->last();

     }



   /**
     * contact view
     *
     * @return View
     */
    public function contact() :View{

        $contactSection  = get_content("content_contact_us")->first();

        $menu         = Menu::where('url', $this->lastSegment )->active()->firstOrfail();

        return view('frontend.contact',[
            'meta_data' => $this->metaData([
                "title"               =>  $menu->meta_title,
                "meta_description"    =>  $menu->meta_description,
                "meta_keywords"       =>  (array) $menu->meta_keywords,
            ]),
            'menu'      => $menu,

            'breadcrumbs'       => ['Home'=>'home',"Contact Us" => null],
            'banner'            => (object) ['title'       => $contactSection->value->breadcrumb_title ,
                                             'description' => $contactSection->value->breadcrumb_description],
            'contact_section' => $contactSection

        ]);
    }


    /**
     * contact store
     *
     * @return View
     */
    public function store(ContactRequest $request) :RedirectResponse{

        $contact           = new Contact();
        $contact->name     = $request->input("name");
        $contact->phone    = $request->input("phone");
        $contact->email    = $request->input("email");
        $contact->subject  = $request->input("subject");
        $contact->message  = $request->input("message");
        $contact->save();

        $route             =  route("admin.contact.list");
        $code =  [
            'type'         => "user contacts",
            'details'      => "email:".$contact->email,
        ];

        $this->send_notification($route,$code);

        return  back()->with(response_status('Contacted successfully'));
    }


    public function send_notification(string $route , array $code){


        $admin             =  Admin::where('super_admin',StatusEnum::true->status())->first();

        $notifications = [
            'database_notifications' => [
                'action' => [SendNotification::class, 'database_notifications'],
                'params' => [
                    [ $admin, 'USER_ACTION', $code, $route ],
                ],
            ],

            'email_notifications' => [
                'action' => [SendMailJob::class, 'dispatch'],
                'params' => [
                    [$admin,'USER_ACTION',$code],

                ],
            ],
            'sms_notifications' => [
                'action' => [SendSmsJob::class, 'dispatch'],
                'params' => [
                    [$admin,'USER_ACTION',$code],
                ],
            ],
        ];


        $this->notify($notifications);


    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function subscribe(Request $request) :RedirectResponse{

        $request->validate([
            'email' =>'required|email|unique:subscribers,email'
        ],[
            "email.unique" => translate("Already Subscribed !!")
        ]);

        $subscriber         = new Subscriber();
        $subscriber->email  = $request->input("email");
        $subscriber->save();


        $route             =  route("admin.subscriber.list");

        $code =  [
            'type'         => "user newsletter subscription",
            'details'      => "email:".$subscriber->email,
        ];

        $this->send_notification($route,$code);
        return  back()->with(response_status('Subscribed successfully'));
    }


    /**
     * Feedback view
     *
     * @return View
     */
    public function feedback() :View{


        $feedbackSection  = get_content("content_feedback")->first();

        $menu = Menu::where('url', $this->lastSegment )->active()->first();

        return view('frontend.feedback',[
            'meta_data' => $this->metaData([
                "title"               =>  $menu->meta_title,
                "meta_description"    =>  $menu->meta_description,
                "meta_keywords"       =>  (array) $menu->meta_keywords,
            ]),
            'menu'      => $menu,
            'breadcrumbs'       => ['Home'=>'home',"Feedback" => null],
            'banner'            => (object) ['title'       => @$feedbackSection->value->breadcrumb_title ,
                                             'description' => @$feedbackSection->value->breadcrumb_description],
            'feedback_section'  => $feedbackSection

        ]);
    }


    /**
     * Feedback Store
     * @param Request $request
     * @return RedirectResponse
     */
    public function feedbackStore(Request $request) :RedirectResponse{

        $request->validate([
            "author"      => ['required',"max:155"],
            "quote"       => ['required',"max:255"],
            "rating"      => ['required','integer',"max:5","min:1"],
            "designation" => ['required',"max:155"],
            "image"       => ["nullable",'image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))]
        ]);

        $requestData = $request->except(['_token','image']);
        $frontend         = new Frontend();
        $frontend->uid    = Str::uuid();
        $frontend->status = StatusEnum::false->status();
        $frontend->key    = "element_testimonial";
        $frontend->value  = $requestData;

        $frontend->saveQuietly();

        $files = [];

        if($request->hasFile("image")){
            $response = $this->storeFile(
                file        : $request->file("image"),
                location    : config("settings")['file_path']['frontend']['path'],
            );
            if(isset($response['status'])){

                $files [] = new File([
                    'name'      => Arr::get($response, 'name', '#'),
                    'disk'      => Arr::get($response, 'disk', 'local'),
                    'type'      => "image",
                    'size'      => Arr::get($response, 'size', ''),
                    'extension' => Arr::get($response, 'extension', ''),
                ]);

            }
        }

        if (!empty($files))     $frontend->file()->saveMany($files);

        $route             =  route("admin.appearance.list","testimonial");

        $code =  [
            'type'         => "User feedback",
            'details'      => "Author:".$request->input("author"),
        ];

        $this->send_notification($route,$code);

        return  back()->with(response_status('Thank you for your feedback! It is under review.'));

    }


}
