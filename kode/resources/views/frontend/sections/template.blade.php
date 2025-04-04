@php
   $templateContent  = get_content("content_template")->first();
   $templateElements = get_content("element_template");
   $templates        = get_feature_templates()->take(6);
   

@endphp


<section class="template-section pb-110">
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-lg-8">
              <div class="section-title-one text-center mb-60" data-aos="fade-up" data-aos-duration="1000">
                <div class="subtitle">{{@$templateContent->value->sub_title}}</div>
                <h2>  @php echo (@$templateContent->value->title) @endphp </h2>
              </div>
          </div>
      </div>

      <div class="row justify-content-center g-4">

        @forelse ($templates  as $template)
     
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="template-item">
                    <div class="radius-one">
                        <img src="{{asset('assets/images/default/template_shape.png')}}" alt="template_shape.png">
                    </div>
                    <div class="radius-two">
                        <img src="{{asset('assets/images/default/template_shape.png')}}" alt="template_shape.png">
                    </div>
                    <div class="icon">
                        <i class="{{@$template->category->icon}}"></i>
                    </div>
                    <div class="content">
                        @if(@$template->category)
                            <div class="category mb-3">
                                {{@$template->category->title ?? '-'}}
                            </div>
                        @endif
                        <h4> {{$template->name}}</h4>
                        <p>{{$template->description}}</p>
                    </div>
                </div>
            </div>
        @empty
          <div class="col-12">
              @include("frontend.partials.not_found")
          </div>
        @endforelse
         
      </div>
  </div>
</section>
