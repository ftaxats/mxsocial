@php
   $content             = get_content("content_faq")->first();
   $elements             = get_content("element_faq");
@endphp

<div class="faq-section pb-110">
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-lg-5">
              <div class="section-title-one text-center mb-60" data-aos="fade-up" data-aos-duration="1000">
                  <div class="subtitle">{{@$content->value->sub_title}}</div>
                  <h2>  @php echo (@$content->value->title) @endphp </h2>
                  <p> {{@$content->value->description}}</p>
              </div>
          </div>
      </div>

      @php
         $splitElements = $elements->split(2); 
      @endphp

      <div class="faq-wrap">

          <div class="accordion" id="accordionFaq">
            <div class="row gy-4">
                @forelse($splitElements  as $faqs)
       
                    <div class="col-lg-6">
                       @forelse ($faqs as $faq)

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-{{$faq->id}}">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{$faq->id}}" aria-expanded="true"
                                        aria-controls="collapse{{$faq->id}}">
                                        {{$faq->value->question}}
                                    </button>
                                </h2>
                                <div id="collapse{{$faq->id}}" class="accordion-collapse collapse {{ $loop->parent->index == 0 &&  $loop->index == 0 ? 'show' : ''}}"
                                     data-bs-parent="#accordionFaq">
                                    <div class="accordion-body">
                                        <p>
                                            {{$faq->value->answer}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                        @empty

                        @endforelse

                    </div>
       
                @empty
                    <div class="col-12">
                        @include("frontend.partials.not_found")
                   </div>
                @endforelse
            </div>
        </div>
      </div>
  </div>
</div>



