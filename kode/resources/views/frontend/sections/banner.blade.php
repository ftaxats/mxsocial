@php

   $bannerContent      = get_content("content_banner")->first();
   $bannerElements     = get_content("element_banner");
   $bannerIMG          = @$bannerContent->file?->where("type",'banner_image')->first();
   $bannerSize         = get_appearance_img_size('banner','content','banner_image');
   $titleVector        = @$bannerContent->file?->where("type",'title_vector_image')->first();
   $titleVectorSize    = get_appearance_img_size('banner','content','title_vector_image');
   $bannerElementSize  = get_appearance_img_size('banner','element','image');

@endphp

<section class="banner-section mb-110">

  <div class="container-fluid px-0">
    <div class="banner-wrapper">
      <div class="row align-items-center gy-5">
          <div class="col-xl-5 col-lg-5">
              <div class="banner-content" data-aos="fade-right" data-aos-duration="1000">
                <h1>
                    @php echo (@$bannerContent->value->title) @endphp
                   <img src="{{imageURL($titleVector,'frontend',true,$titleVectorSize)}}" alt="{{@$titleVector->name??'vector.jpg'}}"></span>
                </h1>
                <p>
                  {{@$bannerContent->value->description}}
                </p>
                <div class="banner-buttons d-flex justify-content-start align-items-center gap-3 flex-wrap">
                      <a href="{{@$bannerContent->value->button_URL}}" class="i-btn btn--lg btn--dark capsuled">
                          {{@$bannerContent->value->button_name}}
                          <span><i class="{{@$bannerContent->value->button_icon}}"></i></span>
                      </a>
                    <div  class="circle-container">
                        <div  class="circleButton">
                            <span>
                                <a id="video-link" data-maxwidth="1000px" data-autoplay="true" data-vbtype="video"  href="{{@$bannerContent->value->video_URL}}">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
              </div>
          </div>
          <div class="col-xl-6 offset-xl-1 col-lg-7">
            <div class="banner-image" data-aos="zoom-in" data-aos-duration="1000">
                <img src="{{imageURL($bannerIMG,'frontend',true,$bannerSize)}}" alt="{{@$titleVector->file->name??'banner.jpg'}}">
            </div>
          </div>
      </div>
    </div>
  </div>

  <div class="sponsors-area">
    <div class="vector-right">
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120" fill="none">
             <path fill-rule="evenodd" clip-rule="evenodd" d="M22.6667 0H0V120H120V97.3333H54.6667C36.9936 97.3333 22.6667 83.0064 22.6667 65.3333V0Z" fill="white"/>
        </svg>
    </div>

    <div class="vector-left">
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120" fill="none">
             <path fill-rule="evenodd" clip-rule="evenodd" d="M22.6667 0H0V120H120V97.3333H54.6667C36.9936 97.3333 22.6667 83.0064 22.6667 65.3333V0Z" fill="white"/>
        </svg>
    </div>

    <div class="swiper sponsor-slider w-100">
        <div class="swiper-wrapper align-items-center">
              @foreach ($bannerElements as $element )
                    @php $file = $element->file?->first(); @endphp
                    <div class="swiper-slide">
                        <div class="sponsor-item">
                            <img src="{{imageURL($file,'frontend',true,$bannerElementSize)}}" alt="{{@$file->name?? 'slider.jpg'}}">
                        </div>
                    </div>
              @endforeach 
        </div>
    </div>

  </div>
</section>





