
@php

   $featureContent        = get_content("content_powerful_feature")->first();
   $featureElements       = get_content("element_powerful_feature");
   $featureImageSize      = get_appearance_img_size('powerful_feature','content','feature_image');
   $featureImage          = @$featureContent->file?->where("type",'feature_image')->first();

@endphp
<section class="power-feature-section pb-110">
    <div class="container">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-6 pe-lg-5 order-lg-1 order-2">
                <img src="{{imageURL($featureImage,'frontend',true,$featureImageSize)}}" class="rounded-5" alt="{{ @$featureImage->name ?? 'feature.jpg'}}">
            </div>
            <div class="col-lg-6 order-lg-2 order-1">
                <div class="section-title-one text-start mb-60" data-aos="fade-right" data-aos-duration="1000">

                    <div class="subtitle">{{@$featureContent->value->sub_title}}</div>
                    <h2>  @php echo (@$featureContent->value->title) @endphp </h2>
                    <p> {{@$featureContent->value->description}}</p>
               
                </div>
                <ul class="power-feature-list">
                      @forelse($featureElements as $feature)
                        <li>
                            <div class="icon">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                            <div class="content">
                                <h5>
                                    {{$feature->value->title}}
                                </h5>
                                <p> {{$feature->value->description}}</p>
                            </div>
                        </li>
                     @empty
                        <li>
                            @include("frontend.partials.not_found")
                        </li>
                     @endforelse
      
                </ul>
            </div>
        </div>
    </div>
</section>