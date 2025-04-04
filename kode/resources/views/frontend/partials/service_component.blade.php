
@if($services->count() > 0)
    <div class="swiper {{$slider}}">
        <div class="swiper-wrapper">
             @foreach ($services as $service)
                 @php $file = $service->file?->first(); @endphp
                    <div class="swiper-slide">
                        <div class="service-item">
                            <div class="image">
                                <img src="{{imageURL($file,'frontend',true,$serviceImageSize)}}" alt="{{@$file->name?? 'feature.jpg'}}">
                            </div>
                            <h4>
                                 {{$service->value->title}}
                            </h4>
                            <p>
                                  {{limit_words(strip_tags($service->value->description),100)}}
                            </p>
                            <a href="{{route('service',['slug' => make_slug($service->value->title) ,'uid'=> $service->uid  ])}}" class="i-btn btn--md btn--white capsuled">
                                  {{translate('More info')}}
                                <span><i class="bi bi-arrow-up-right"></i></span>
                            </a>
                        </div>
                    </div>
             @endforeach
        </div>
    </div>
@else
    @include("frontend.partials.not_found")
@endif
