@php
   $footer        = get_content("content_footer")->first();
   $footerbg      = $footer->file->where("type",'footer_background')->first();
   $footerbgSize  = get_appearance_img_size('footer','content','footer_background');
   $paymentImg      = $footer->file->where("type",'payment_image')->first();
   $paymentImgSize  = get_appearance_img_size('footer','content','payment_image');
   $icons         = get_content("element_social_icon");
   $buttons       = get_content("element_footer");
   $blogs        =get_feature_blogs()->take(2);
   $services = get_content("element_service")->take(4);
@endphp

<footer>
  <div class="container">
    <div class="footer-top pt-110 pb-110">
        <div class="row justify-content-center">
          <div class="col-lg-9">
              <div class="footer-top-content" data-aos="fade-up" data-aos-duration="1000">
                   <img src="{{imageURL($footerbg,'frontend',true,$footerbgSize)}}" alt="{{@$footerbg->name ?? 'footer-bg.jpg'}}" class="footer-top-img">
                    <h2>
                       {{ @$footer->value->title}}
                    </h2>
                    <p> {{ @$footer->value->description}} </p>


                    @if( $buttons->count() > 0)
                      <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            @foreach ($buttons as $button)
                                  <a href="{{ @$button->value->button_URL }}" class="i-btn btn--lg btn--primary capsuled">
                                    {{ @$button->value->button_name }}
                                    <span>
                                        <i class=" {{ @$button->value->button_icon }}"></i>
                                    </span>
                                  </a>
                            @endforeach
                      </div>
                    @endif
              </div>
          </div>
        </div>
    </div>
  </div>

  <div class="container-fluid px-lg-0 px-md-4">
    <div class="news-letter-area">
        <div class="newsletter-wrapper">
          <form  action="{{route('subscribe')}}" method="post">
             @csrf
              <input name="email" type="email" placeholder="{{translate('Enter your email')}}">
              <button class="i-btn btn--lg btn--primary capsuled">
                   {{translate("SUBSCRIBE")}}
                  <span><i class="bi bi-arrow-up-right"></i></span>
              </button>
          </form>
        </div>
    </div>
  </div>

  <div class="container">
      <div class="footer-bottom">
        <div class="row gy-5">
          @if($menus->count() > 0)
              <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                  <h4 class="footer-title">
                     {{translate('Quick link')}}
                  </h4>
                  <ul class="footer-list">
                     @foreach ($menus as $menu)
                            <li>
                                <a href="{{url($menu->url)}}">
                                    {{$menu->name}}
                                </a>
                            </li>
                      @endforeach
                  </ul>
              </div>
          @endif

          @if($pages->count() > 0)
              <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                  <h4 class="footer-title">
                      {{translate("Information")}}
                  </h4>
                  <ul class="footer-list">
                      @foreach ($pages as $page)
                          <li>
                              <a href="{{route('page',$page->slug)}}">
                                {{$page->title}}
                              </a>
                          </li>
                      @endforeach
                  </ul>
              </div>
          @endif

          @if($services->count() > 0)
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                  <h4 class="footer-title">Services</h4>
                  <ul class="footer-list">
                      @foreach($services as $service)
                         <li><a href="{{route('service',['slug' => make_slug($service->value->title) ,'uid'=> $service->uid  ])}}"> {{limit_words($service->value->title,25)}}</a></li>
                      @endforeach
                  </ul>
              </div>
          @endif

           @if($blogs->count() > 0)
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                  <h4 class="footer-title">
                     {{translate("Blogs")}}
                  </h4>
                  <ul class="footer-list">
                       @foreach ($blogs as $blog)
                          <li>
                            <a href="{{route('blog.details',$blog->slug)}}">{{limit_words($blog->title,28)}}</a>
                            <span>
                                {{get_date_time($blog->created_at,"F j, Y")}}
                            </span>
                        </li>
                       @endforeach
                  </ul>
              </div>
           @endif

        </div>
      </div>

      <div class="copyright-area d-flex justify-content-lg-between justify-content-center align-items-center flex-wrap gap-4">

           @if($icons->count() > 0)
              <div class="footer-social">
                  <ul>
                       @foreach ($icons as $icon)
                            <li><a target="_blank" href="{{$icon->value->button_url}}"><i class="{{ $icon->value->icon }}"></i></a></li>
                       @endforeach

                  </ul>
              </div>
            @endif

            <div class="payment-image">
                <img src="{{imageURL($paymentImg ,'frontend',true,$paymentImgSize)}}" alt="{{ @$paymentImg->name ?? 'payment.jpg' }}">
            </div>


            <div class="copyright">
               <p class="mb-0 text-white opacity-75 fs-14 lh-1">{{site_settings("copy_right_text")}}</p>
            </div>
      </div>
  </div>
</footer>


