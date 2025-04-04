@php
   $authElements   = get_content("element_authentication_section");
@endphp

<div class="col-xl-5 col-lg-5">
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="auth-slider-wrapper">
                <div class="swiper auth-slider">
                    <div class="swiper-wrapper">
                        @foreach ( $authElements  as $element )
                            <div class="swiper-slide">
                                <div class="auth-slider-item">
                                    <div class="mb-5">
                                        @foreach (@get_appearance()->authentication_section->element->images as  $key => $val)
                                                @php
                                                    $file =  $element->file->where("type",$key)->first();
                                                @endphp
                                                <div class="platform-content-img">
                                                <img
                                                    src="{{imageURL(@$file,'frontend',true,$val->size)}}"
                                                    alt="{{@$file->name}}"
                                                    loading="lazy"/>
                                                </div>
                                         @endforeach
                                    </div>

                                    <h4>
                                        {{@$element->value->title}}
                                    </h4>
                                    <p>
                                        {{@$element->value->description}}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>
