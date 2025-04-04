@extends('layouts.master')
@section('content')

@include("frontend.partials.breadcrumb")



<section class="service-details-section pb-110">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-8 pe-lg-5">
                <div class="text-editor-content">
                    @php echo $service->value->description @endphp
                </div>     
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar-wigt mb-4">
                     @php    $services = get_content("element_service")->where("uid",'!=',$service->uid) @endphp
                    <h4>
                         {{translate('Other Services')}}
                    </h4>
                    <ul>
                        @forelse ($services as $service)
                            <li>
                                <a href="{{route('service',['slug' => make_slug($service->value->title) ,'uid'=> $service->uid  ])}}"><i class="bi bi-caret-right-fill"></i>
                                     {{limit_words($service->value->title,25)}}
                               </a>
                           </li>  
                        @empty
                            <li>
                                   @include("frontend.partials.not_found") 
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="sidebar-wigt">
                    <h4>
                         {{translate('Contact Us')}}
                    </h4>
                    <form class="w-100"  action="{{route('contact.store')}}"  method="post">
                        @csrf
                        <div class="form-inner">
                            <input required placeholder="{{translate('Enter your name')}}" name="name" value="{{old('name')}}" type="text"
                            id="name"/>
                        </div>
                        <div class="form-inner">
                            <input required placeholder="{{translate('Enter your phone')}}" name="phone" value="{{old('phone')}}" type="text" id="phone"/>
                        </div>
                        <div class="form-inner">
                            <input required placeholder="{{translate('Enter your email')}}" name="email" value="{{old('email')}}" type="email" id="email"/>
                        </div>
                        <div class="form-inner">
                            <input required placeholder="{{translate('Enter your subject')}}" name="subject" value="{{old('subject')}}" type="text" id="subject"/>
                        </div>
                        <div class="form-inner">
                            <textarea placeholder="{{translate('Write Message')}}" name="message">{{old('message')}}</textarea>
                        </div>
                        <button type="submit" class="i-btn btn--lg btn--primary capsuled w-100">
                            {{translate('Submit')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

