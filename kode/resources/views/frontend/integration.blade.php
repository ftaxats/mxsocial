@extends('layouts.master')
@section('content')


@include("frontend.partials.breadcrumb")


@php

   $feed          = $section->childrens?->where('key','content_integration_details')->first();
   $feedElements  = $section->childrens?->where('key','!=','content_integration_details');
   $featureImage        = @$feed->file?->where("type",'feature_image')->first();
   $featureImageSize    = get_appearance_img_size('integration_details','content','feature_image');

@endphp

<section class="social-feed pb-110">
    <div class="container">

        @if($feed)
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="section-title-one text-center mb-60" data-aos="fade-up" data-aos-duration="1000">
                        <div class="subtitle">{{@$feed->value->sub_title}}</div>
                        <h2>  @php echo @$feed->value->title @endphp </h2>
                    
                    </div>
                </div>
            </div>
        @endif

        @forelse ($feedElements as $feed)

            @if($loop->index  == 0)
                    <div class="row justify-content-center">
                        <div class="col-lg-4 mb-4 position-relative">
                            <div class="star-one">
                                <img src="{{asset('assets/images/default/star_top.jpg')}}" alt="star_top.jpg">
                            </div>
                            <div class="star-two">
                                <img src="{{asset('assets/images/default/star_bottom.jpg')}}" alt="star_bottom.jpg">
                            </div>
                            <div class="arrow-one">
                                <img src="{{asset('assets/images/default/arrow_up.jpg')}}" alt="arrow_up.jpg">
                            </div>
                            <div class="feed-item">
                                <div class="serial">
                                    {{$loop->iteration}} 
                                </div>
                                <div class="text">
                                    <p>   {{$feed->value->description}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
            @elseif($loop->index  == 1)

                <div class="row justify-content-center">
                    <div class="col-lg-10 mb-4">
                        <div class="row align-items-center gy-5">
                            <div class="col-lg-5">
                                <div class="feed-item">
                                    <div class="serial">
                                        {{$loop->iteration}} 

                                    </div>
                                    <div class="text">
                                        <p>   {{$feed->value->description}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <img src="{{imageURL($featureImage,'frontend',true,$featureImageSize)}}" alt="{{@$featureImage->file->name??'feature.jpg'}}">
                            </div>
                        </div>
                    </div>
                </div>

             @else
                <div class="row justify-content-center mb-2">
                    <div class="col-lg-4 position-relative">
                        <div class="arrow-two">
                            <img src="{{asset('assets/images/default/arrow_down.jpg')}}" alt="arrow_down.jpg">
                        </div>
                        <div class="star-five">
                            <img src="{{asset('assets/images/default/star_middle.jpg')}}" alt="star_middle.jpg">
                        </div>
                        <div class="star-six">
                            <img src="{{asset('assets/images/default/star_bottom.jpg')}}" alt="star_bottom.jpg">
                        </div>
                        <div class="feed-item">
                            <div class="serial">
                                {{$loop->iteration}} 

                            </div>
                            <div class="text">
                                <p>   {{$feed->value->description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
        @empty
             <div class="row">
                 <div class="col-12">
                     @include("frontend.partials.not_found")
                 </div>
             </div>
        @endforelse
       
    </div>
</section>




@endsection

