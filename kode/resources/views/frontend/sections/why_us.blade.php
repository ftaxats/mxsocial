@php
   $content             = get_content("content_why_us")->first();
   $elements             = get_content("element_why_us");
@endphp


<section class="choose pb-110">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-title-one text-center mb-60" data-aos="fade-up" data-aos-duration="1000">

                    <div class="subtitle">{{@$content->value->sub_title}}</div>
                    <h2>  @php echo ($content?->value?->title ?? '--') @endphp </h2>
                  
                </div>
            </div>
        </div>
        <div class="row g-5">
            <div class="col-lg-12">
                <div class="row g-4 justify-content-center choose-card-wrapper">
                    @foreach ($elements  as $element)
                      <div class="col-lg-4 col-md-6">
                        <div class="choose-card">
                          <div class="choose-card-icon">
                              <i class="{{$element->value->icon}}"></i>
                          </div>
                          <div>
                                <h4>
                                    {{@$element->value->title}}
                                </h4>
                                <p>
                                    {{@$element->value->description}}
                                </p>
                          </div>
                        </div>
                      </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
