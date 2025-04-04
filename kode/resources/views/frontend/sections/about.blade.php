@php
   $aboutContent  = get_content("content_about")->first();
   $aboutElements = get_content("element_about");

   $aboutCounters = get_content("element_about_counter");

@endphp

<section class="about-section pb-110">
  <div class="container">
    <div class="row gy-5">
      <div class="col-xl-5 pe-lg-5">
        <div class="section-title-one text-start mb-60" data-aos="fade-right" data-aos-duration="1000">
            <div class="subtitle">{{@$aboutContent->value->sub_title}}</div>
            <h2>  @php echo @$aboutContent->value->title @endphp </h2>
            <p> {{@$aboutContent->value->description}}</p>
        </div>
        <div class="counter-wrapper">
          <div class="row">
                @forelse($aboutCounters as $counter)
                  <div class="col-md-6">
                    <div class="counter-single text-center">
                        <div class="counter-text d-flex flex-column">
                            <div class="d-flex flex-row justify-content-center align-items-center gap-2">
                                <h3> {{$counter->value->counter_value}}</h3><i class="bi bi-plus-lg"></i>
                            </div>
                            <p>
                              {{@$counter->value->counter_text}}
                            </p>
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
      </div>
      <div class="col-xl-7">
          
            @if($aboutElements->count() > 0)
                <div class="about-card-wrapper">
                  <div class="center-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" xml:space="preserve" ><g><path d="m476.855 307.148-29.937-29.933-42.426 42.426 29.938 29.933c23.39 23.395 23.39 61.465 0 84.856-23.39 23.39-61.461 23.39-84.856 0L192.36 277.215l-42.425 42.426 157.214 157.214c46.86 46.86 122.848 46.86 169.707 0s46.86-122.847 0-169.707zm0 0"  opacity="1" data-original="#000000" ></path><path d="M162.426 434.43c-23.395 23.39-61.465 23.39-84.856 0-23.39-23.39-23.39-61.461 0-84.856L234.785 192.36l-42.426-42.425L35.145 307.148c-46.86 46.86-46.86 122.848 0 169.707s122.847 46.86 169.707 0l29.933-29.937-42.426-42.426zM349.574 77.57c23.395-23.39 61.465-23.39 84.856 0 23.39 23.39 23.39 61.461 0 84.856L277.215 319.64l42.426 42.425 157.214-157.214c46.86-46.86 46.86-122.848 0-169.707s-122.847-46.86-169.707 0l-29.933 29.937 42.426 42.426zm0 0"  opacity="1" data-original="#000000"></path><path d="m65.082 234.785 42.426-42.426-29.938-29.933c-23.39-23.395-23.39-61.465 0-84.856 23.39-23.39 61.461-23.39 84.856 0l163.426 163.426 42.425-42.426L204.852 35.145c-46.86-46.86-122.848-46.86-169.707 0s-46.86 122.847 0 169.707zm0 0"  opacity="1" data-original="#000000"></path></g></svg>
                  </div>
                  <div class="row g-md-5 g-4">

                      @foreach ($aboutElements as $about)

                            <div class="col-md-6">
                              <div class="about-card-item">
                                <div class="icon">
                                     <i class="{{$about->value->icon}}"></i>
                                </div>
                                <div class="content">
                                  <h4>{{@$about->value->title}}</h4>
                                  <p>{{@$about->value->description}}</p>
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
    </div>
  </div>
</section>