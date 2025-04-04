@extends('layouts.master')
@section('content')

@php

   $file         = $feedback_section->file->where("type",'image')->first();
   $size         = get_appearance_img_size('feedback','content','image');

@endphp


@include("frontend.partials.breadcrumb")

<section class="contact feedback pb-110">
  <div class="container">
    <div class="contact-wrapper linear-bg">
      <div class="row align-items-center g-5">
        <div class="col-lg-5">
            <div class="contact-left gs_reveal fromLeft">
              <div class="review-lef-image">
                    <img src="{{imageURL($file,'frontend',true,$size)}}" alt="{{@$file->name??'feedback.jpg'}}">
              </div>
                <div class="section-title light mb-50 text-center">
                      <h3 class="mt-0 mb-3">{{@$feedback_section->value->heading}}</h3>
                      <p>
                          {{@$feedback_section->value->description}}
                      </p>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
          <div class="contact-form-wrapper">
            <form action="{{route('feedback.store')}}" class="contact-form ms-xl-5 gs_reveal fromRight" method="post" enctype="multipart/form-data">
              @csrf
              <div>
                <h4 class="mb-4">
                    {{Arr::get($meta_data,"title",'How do you rate our service')}}
                </h4>

                  <div class="rating">
                      @for($i = 5 ; $i>=1 ;$i--)
                          <input required type="radio" value="{{$i}}" @if($i==1) checked  @endif name="rating" id="rating-{{$i}}">
                          <label for="rating-{{$i}}">
                          </label>
                      @endfor
                  </div>
              </div>
              <div class="row gx-4 gy-5 mt-1">
                <div class="col-lg-6">
                  <div class="form__group field">
                    <input
                      required
                      placeholder="{{translate('Name')}}"
                      class="form__field"
                      name="author"
                      value="{{old('author')}}"
                      type="text"
                      id="author"/>

                    <label class="form__label" for="author">
                        {{translate("Name")}}
                    </label>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form__group field">
                    <input
                      required
                      placeholder="{{translate('Designation')}}"
                      id="designation"
                      class="form__field"
                      type="text"
                      name="designation"
                      value="{{old('designation')}}"/>
                    <label class="form__label" for="designation">
                        {{translate("Designation")}}
                    </label>
                  </div>
                </div>
                <div class="col-12">
                    <label class="mb-2"> {{translate("Upload Image")}}</label>
                    <div>
                        <label  for="image" class="feedback-file">
                            <input hidden  data-size = "100x100" type="file" name="image" id="image" class="preview">
                            <span><i class="bi bi-image"></i> 
                                {{translate("Select image")}}
                            </span>
                        </label>
                        <div class="image-preview-section">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                  <div class="form__group field">
                    <textarea placeholder="{{translate('Message')}}" required  class="form__field" id="quote" name="quote">{{old('quote')}}</textarea>
                      <label class="form__label" for="quote">
                        {{translate("Write your Message")}}
                      </label>
                  </div>
                </div>
                <div class="col-12">
                  <button  class="i-btn btn--primary btn--lg capsuled">
                        {{translate("Submit")}}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('frontend.partials.page_section')


@endsection

