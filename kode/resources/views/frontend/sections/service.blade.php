@php

$content = get_content("content_service")->first();
$services = get_content("element_service");
$serviceImageSize = get_appearance_img_size('service','element','image');

@endphp


<section class="serivce-section pb-110">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section-title-one text-center mb-60" data-aos="fade-up" data-aos-duration="1000">
                    <div class="subtitle">{{@$content->value->sub_title}}</div>
                    <h2> @php echo (@$content->value->title) @endphp </h2>
                    <p> {{@$content->value->description}}</p>
                </div>
            </div>
        </div>
        <div class="service-wrapper position-relative">
            <svg width="1220" height="1390" viewBox="0 0 1220 1390" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_552_2)">
                    <mask id="mask0_552_2" maskUnits="userSpaceOnUse" x="0" y="0"
                        width="1220" height="1390">
                        <path d="M1220 0H0V1390H1220V0Z" fill="white" />
                    </mask>
                    <g mask="url(#mask0_552_2)">
                        <path
                            d="M1 1390V1324.48C1 1309.91 15.3269 1298.1 33 1298.1H1185C1202.67 1298.1 1217 1286.29 1217 1271.73V655.234C1217 640.667 1202.67 628.859 1185 628.859H33.021C15.3397 628.859 1.00945 617.041 1.02104 602.468L1.50015 0"
                            stroke="currentColor" stroke-opacity="0.15" stroke-width="3" />
                        <path
                            d="M1 1390V1324.48C1 1309.91 15.3269 1298.1 33 1298.1H1185C1202.67 1298.1 1217 1286.29 1217 1271.73V655.234C1217 640.667 1202.67 628.859 1185 628.859H33.021C15.3397 628.859 1.00945 617.041 1.02104 602.468L1.50015 0"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-dasharray="0.05 1" />
                    </g>
                </g>
                <defs>
                    <clipPath id="clip0_552_2">
                        <rect width="1220" height="1390" fill="white" />
                    </clipPath>
                </defs>
            </svg>

            <div class="row h-625">
                <div class="col-lg-1 d-lg-block d-none">
                    <div class="service-type-icon">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
                <div class="col-lg-11">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="section-title-two text-start mb-60">
                                <h2> @php echo (@$content->value->section_top_title) @endphp </h2>
                                <p>{{@$content->value->section_top_description}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-12">
                            @include('frontend.partials.service_component',[ 'slider' => "service-slider-one",'services'
                            => $services->take(6)])
                        </div>
                    </div>
                </div>
            </div>

            <div class="row h-625 mt-5" id="service-slider-two-sec">
                <div class="col-lg-11">
                    <div class="row justify-content-end">
                        <div class="col-lg-10">
                            <div class="section-title-two text-end mb-60">
                                <h2> @php echo (@$content->value->section_bottom_title) @endphp </h2>
                                <p>{{@$content->value->section_bottom_description}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-12">
                            @include('frontend.partials.service_component',[ 'slider' => "service-slider-two",'services'
                            => $services->skip(6)->take(PHP_INT_MAX)])
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 d-lg-block d-none">
                    <div class="service-type-icon">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
            </div>

            <div class="row mt-5" id="service-tab-sec">
                <div class="col-lg-1 d-lg-block d-none">
                    <div class="service-type-icon">
                        <i class="bi bi-laptop"></i>
                    </div>
                </div>
                <div class="col-lg-11">
                    @php
                    $insight = get_content("content_service_insight")->first();
                    $insightElements = get_content("element_service_insight");
                    $featureImageSize = get_appearance_img_size('service_insight','element','image');

                    @endphp

                    <div class="row">
                        <div class="col-lg-10">
                            <div class="section-title-two text-start mb-60">
                                <h2> @php echo (@$insight->value->title) @endphp </h2>
                                <p> {{@$insight->value->short_description}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="service-tab-wrapper">

                        @if($insightElements->count() > 0)
                          <ul class="nav nav-tabs style-7 gap-lg-4 gap-2 mb-30" id="insightTab" role="tablist">
                              @foreach ($insightElements as $insightElement)

                              <li class="nav-item" role="presentation">
                                  <button class="nav-link {{$loop->index == 0 ? 'active' : ''}} "
                                      id="tab-insight-{{$loop->index}}" data-bs-toggle="tab"
                                      data-bs-target="#tab-insight-{{$loop->index}}-pane" type="button" role="tab"
                                      aria-controls="tab-insight-{{$loop->index}}-pane" aria-selected="true">
                                      {{$insightElement->value->sub_title}}
                                      <span>
                                          <i class="bi bi-arrow-up-right"> </i>
                                      </span>
                                  </button>
                              </li>

                              @endforeach
                          </ul>
                          <div class="tab-content" id="insightTabContent">

                              @foreach ($insightElements as $insightElement)
                                <div class="tab-pane fade {{$loop->index == 0 ? 'show active' : ''}}"
                                    id="tab-insight-{{$loop->index}}-pane" role="tabpanel"
                                    aria-labelledby="tab-insight-{{$loop->index}}" tabindex="0">
                                    <div class="mb-4">
                                        @php $file = $insightElement->file?->first(); @endphp
                                        <h5 class="mb-2">{{$insightElement->value->title}}</h5>
                                        <p>{{$insightElement->value->description}}</p>
                                    </div>
                                    <img src="{{imageURL($file,'frontend',true,$featureImageSize)}}"
                                        alt="{{@$file->name?? 'feature.jpg'}}" class="rounded-4">
                                </div>
                              @endforeach

                          </div>
                        @else
                            @include("frontend.partials.not_found")
                        @endif
                    </div>
                </div>
            </div>

          </div>
    </div>
</section>