

@php
   $content              = get_content("content_team")->first();
   $elements             = get_content("element_team");

   $teamImageSize  = get_appearance_img_size('team','element','image');
@endphp


<section class="team-section pb-110">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="section-title-one text-center mb-60" data-aos="fade-up" data-aos-duration="1000">
            <div class="subtitle">{{@$content->value->sub_title}}</div>
            <h2>  @php echo (@$content->value->title) @endphp </h2>
            <p> {{@$content->value->description}}</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
            @if($elements->count() > 0)
                <div class="swiper team-slider">
                    <div class="swiper-wrapper">

                        @foreach ($elements as $element )
                                @php $file = $element->file?->first(); @endphp
                                <div class="swiper-slide">
                                    <div class="team-item">
                                        <img src="{{imageURL($file,'frontend',true,$teamImageSize)}}" alt="{{@$file->name ?? 'team.jpg'}}">
                                    </div>
                                </div>
                        @endforeach 

                      </div>
                </div>

            @else
               @include("frontend.partials.not_found")
            @endif
        
        </div>
      </div>
    </div>
  </section>