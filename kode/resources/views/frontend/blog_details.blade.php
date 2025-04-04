@extends('layouts.master')
@section('content')

@php
   $blogContent  = get_content("content_blog")->first();
   $newsLetter  = get_content("content_newsletter")->first();

@endphp

@include("frontend.partials.breadcrumb")

<section class="blog-details pb-110">
  <div class="container">
    <h3 class="title"> {{$blog->title}} </h3>
    <div class="d-flex gap-4 align-items-center mb-30">
        <ul class="date">
            <li>{{get_date_time($blog->created_at,"F j, Y")}}</li>
            <li>{{get_date_time($blog->created_at," g a")}}</li>
        </ul>
    
    </div>

    <div class="mb-30 blog-d-image">
      <img src='{{imageURL(@$blog->file,"blog",true)}}'
      alt="{{@$blog->file->name ?? 'blog-image.jpg'}}">
    </div>

    <div class="row gy-5">
      <div class="col-lg-8 pe-lg-5">
          @php  echo @$blog->description @endphp

          <div class="share-blog mt-5">
            <h6>
               {{translate('Like what you see? Share with a friend.')}}
            </h6>
            
            <div class="footer-social">
                <ul>
                  <li>
                      <a class="social-share " 
                        href="javascript:void(0)"
                        data-url="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                        data-name="Facebook" 
                        data-width="600" 
                        data-height="300">
                          <i class="bi bi-facebook"></i>
                      </a>
                  </li>
                  <li>
                      <a class="social-share" 
                          href="javascript:void(0)"
                        data-url="http://twitter.com/share?text={{ urlencode($blog->slug) }}&url={{ urlencode(url()->current()) }}" 
                        data-name="Twitter" 
                        data-width="600" 
                        data-height="450">
                          <i class="bi bi-twitter"></i>
                      </a>
                  </li>
                  <li>
                      <a class="social-share" 
                          href="javascript:void(0)"
                        data-url="https://api.whatsapp.com/send?text={{ urlencode($blog->slug) }}" 
                        data-name="WhatsApp" 
                        data-width="700" 
                        data-height="650">
                          <i class="bi bi-whatsapp"></i>
                      </a>
                  </li>
                  <li>
                      <a class="social-share" 
                        href="javascript:void(0)"
                        data-url="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                        data-name="Linkedin" 
                        data-width="600" 
                        data-height="450">
                          <i class="bi bi-linkedin"></i>
                      </a>
                  </li>
                  <li>
                      <a class="social-share" 
                        data-url="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}" 
                        href="javascript:void(0)"
                        data-name="Telegram" 
                        data-width="600" 
                        data-height="450">
                          <i class="bi bi-telegram"></i>
                      </a>
                  </li>
                  <li>
                      <a href="mailto:?subject={{ $blog->title }}&amp;body={{ urlencode(url()->current()) }}">
                          <i class="bi bi-envelope-at-fill"></i>
                      </a>
                  </li>
                </ul>
            
            </div>
          </div>
      </div>

      <div class="col-lg-4">
          <h5 class="mb-4 text-uppercase">{{translate("Related Resources")}}</h5>
          <ul class="popular-post-list">
            @forelse($related_blogs as $blog)
                <li>
                    <div class="image">
                          <img  src='{{imageURL(@$blog->file,"blog",true)}}'
                          alt="{{@$blog->file->name ?? 'blog-image.jpg'}}">
                    </div>
                    <div class="content">
                
                          <h6> <a href="{{route('blog.details',$blog->slug)}}"> {{limit_words($blog->title,28)}}</a></h6>
                    </div>
                </li>
            @empty
                  <li class="text--center">
                      {{translate('No data found !!')}}
                  </li>
            @endforelse
          </ul>
      </div>
    </div>
  </div>
</section>

@endsection

