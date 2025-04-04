@php
    $testimonialContent    = get_content("content_testimonial")->first();
    $testimonialElements   = get_content("element_testimonial");

    $featureImageSize      = get_appearance_img_size('testimonial','element','image');

@endphp

<section class="reviews pb-110">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="section-title-one text-center mb-60" data-aos="fade-up" data-aos-duration="1000">
                    <div class="subtitle">{{@$testimonialContent->value->sub_title}}</div>
                    <h2>  @php echo (@$testimonialContent->value->title) @endphp </h2>
                    <p>{{@$testimonialContent->value->description}}.</p>
                </div>
            </div>
        </div>

        <div class="row g-lg-4 g-0 align-items-center">
            <div class="col-12">
                 @if($testimonialElements->count() > 0)
                        <div class="review-wrapper">
                            <div class="shape-radius-one">
                                <img src="{{asset('assets/images/default/template_shape.png')}}" alt="shape.png">
                            </div>
                            <div class="shape-radius-two">
                                <img src="{{asset('assets/images/default/template_shape.png')}}" alt="shape.png">
                            </div>
                            <div class="swiper review-slider">
                                <div class="swiper-wrapper">
                                    @foreach ($testimonialElements as $testimonial)
                                        <div class="swiper-slide">
                                            <div class="review-card">
                                                <div class="quote-icon quote-one">
                                                    <i class="bi bi-quote"></i>
                                                </div>
                                                <div
                                                    class="d-flex flex-row justify-content-start align-items-stretch flex-md-nowrap flex-wrap gap-0">
                                                    <div class="review-image">
                                                        @php $file = $testimonial->file?->first(); @endphp
                                                        <img src="{{imageURL($file,'frontend',true,$featureImageSize)}}" alt="{{@$file->name?? 'author.jpg'}}">
                                                    </div>
                                                    <div class="review-content">
                                                        <ul class="review-rating d-flex align-items-center gap-1">
                                                            @php echo (show_ratings($testimonial->value->rating)) @endphp
                                                        </ul>
                                                        <p>
                                                            {{$testimonial->value->quote}}
                                                        </p>
                                                        <div class="reviewer-meta">
                                                            <h6>{{$testimonial->value->author}}</h6>
                                                            <span>{{$testimonial->value->designation}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                        
                                </div>
                            </div>
                            <div class="review-arrow-wrapper">
                                <div class="review-button-prev"><i class="bi bi-arrow-left"></i></div>
                                <div class="review-button-next"><i class="bi bi-arrow-right"></i></div>
                            </div>
                        </div>
                @else
                       @include("frontend.partials.not_found")
                @endif
            </div>
        </div>
    </div>
</section>






