
@php
   $content  = get_content("content_plan")->first();
   $plans    = App\Models\Package::active()
                                        ->feature()
                                        ->get();

@endphp
<section class="pricing-plan pb-110">
  <div class="container">
  
      <div class="row justify-content-start align-items-center mb-60 g-4">
        <div class="col-lg-6">
            <div class="section-title-one text-start" data-aos="fade-right" data-aos-duration="1000">
                <div class="subtitle">{{@$content->value->sub_title}}</div>
                <h2>
                     @php echo (@$content->value->title) @endphp
                </h2>
                <p>{{@$content->value->description}}</p>
            </div>
        </div>
        <div class="col-lg-6 d-flex justify-content-end align-items-center">
            <a href="{{url(@$content->value->button_URL)}}" class="i-btn btn--lg btn--white capsuled"> {{@$content->value->button_name}}  <span><i
                        class="bi bi-arrow-up-right"></i></span></a>
        </div>
     </div>

      @include("frontend.partials.plan_component")

  </div>
</section>

