
@php

   $featureContent   = get_content("content_feature")->first();
   $featureElements  = get_content("element_feature");
   $featureImageSize  = get_appearance_img_size('feature','content','feature_image');

   $featureImage          = @$featureContent->file?->where("type",'feature_image')->first();


@endphp

<section class="feature-section pb-110">
  <div class="container">
      <div class="row justify-content-start">
          <div class="col-lg-8 col-xl-6">
              <div class="section-title-one text-start mb-60" data-aos="fade-right" data-aos-duration="1000">
                  <div class="subtitle">{{@$featureContent->value->sub_title}}</div>
                  <h2>  @php echo (@$featureContent->value->title) @endphp </h2>
              </div>
          </div>
      </div>
      <div class="row align-items-center gy-5">
          <div class="col-lg-5">
              @if($featureElements->count() > 0)
                  <div class="faq-wrap-two">
                      <div class="accordion" id="featureAccordion">

                        @foreach ($featureElements as $feature )

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{$loop->index != 0 ? 'collapsed' :''}}" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-feature-{{$loop->index}}" aria-expanded="true" aria-controls="collapse-feature-{{$loop->index}}">
                                        {{$feature->value->title}}
                                    </button>
                                </h2>
                                <div id="collapse-feature-{{$loop->index}}" class="accordion-collapse collapse {{$loop->index == 0 ? 'show' :''}} "
                                    data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        {{$feature->value->description}}
                                    </div>
                                </div>
                            </div>
          
                        @endforeach
                      
                      
                      </div>
                  </div>
              @else
                   @include("frontend.partials.not_found")
              @endif
          </div>
          <div class="col-lg-7">
              <img src="{{imageURL($featureImage,'frontend',true,$featureImageSize)}}" class="rounded-5" alt="{{ @$featureImage->name ?? 'feature.jpg'}}" data-aos="fade-left" data-aos-duration="1000">
          </div>
      </div>
  </div>
</section>
