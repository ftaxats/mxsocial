
@php
   $blogContent  = get_content("content_blog")->first();
   $blogs        = get_feature_blogs()->take(4);
   
@endphp

<section class="blog-section pb-110">
  <div class="container">
      <div class="row justify-content-start align-items-center mb-60 g-4">
          <div class="col-lg-6">
              <div class="section-title-one text-start" data-aos="fade-right" data-aos-duration="1000">
                  <div class="subtitle">{{@$blogContent->value->sub_title}}</div>
                  <h2>
                     @php echo (@$blogContent->value->title) @endphp
                  </h2>
                  <p>{{@$blogContent->value->description}}</p>
              </div>
          </div>
          <div class="col-lg-6 d-flex justify-content-end align-items-center">
              <a href="{{url(@$blogContent->value->button_URL)}}" class="i-btn btn--lg btn--white capsuled">{{@$blogContent->value->button_name}}<span><i
                          class="bi bi-arrow-up-right"></i></span></a>
          </div>
      </div>
      <div class="row g-xl-5 g-4">
          @forelse ($blogs as $blog)
              @include("frontend.partials.blog_component")
          @empty
              <div class="col-12">
                   @include("frontend.partials.not_found")
              </div>
          @endforelse
      </div>

  </div>
</section>