@extends('layouts.master')
@section('content')

@php

   $file         = $contact_section->file->where("type",'image')->first();
   $size         = get_appearance_img_size('contact_us','content','image');

@endphp


@include("frontend.partials.breadcrumb")


<section class="contact pb-110">
  <div class="container">
    <div class="existing-customer linear-bg">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5> {{@$contact_section->value->support_title}}</h5>
                      <p>
                          {{@$contact_section->value->support_description}}
                      </p>
                </div>
                <a href="{{url(@$contact_section->value->button_url)}}"
                  class="i-btn btn--white btn--lg capsuled">
                    {{@$contact_section->value->button_name}}
                </a>
          </div>
    </div>

    <div class="contact-wrapper linear-bg">
          <div class="row g-5">
            <div class="col-lg-6">
              <div class="contact-left gs_reveal fromLeft">
                    <div class="contact-left-img">
                          <img src="{{imageURL($file,'frontend',true,$size)}}" alt="{{@$file->name??'contact.jpg'}}">
                    </div>
                <div class="section-title light mb-5">
                     <h3 class="mt-0 mb-3">{{@$contact_section->value->section_heading}}</h3>
                    <p>
                          {{@$contact_section->value->section_description}}
                    </p>
                </div>
                <ul class="contact-list">
                    <li>
                      <span><i class="bi bi-envelope-open"></i></span>
                        <div>
                            <a href="mailto:{{site_settings('email')}}">
                                {{site_settings("email")}}
                            </a>
                        </div>
                    </li>
                    <li>
                      <span><i class="bi bi-telephone"></i></span>
                          <div>
                            <a href="tel:{{site_settings('phone')}}"> {{site_settings("phone")}}</a>
                          </div>
                    </li>
                    <li>
                        <span><i class="bi bi-geo-alt"></i></span>
                        <div>
                          <p class="text-dark fw-medium"> {{site_settings("address")}}</p>
                        </div>
                    </li>
                    <li>
                         <span><i class="bi bi-clock"></i></span>
                        <div>
                            <p class="text-dark fw-medium"> {{@$contact_section->value->opening_hour_text}} </p>
                        </div>
                    </li>
                </ul>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="contact-form-wrapper">
                <form action="{{route('contact.store')}}" class="contact-form gs_reveal fromRight" method="post">
                      @csrf
                      <h4>  {{$contact_section->value->section_title}}</h4>
                      <div class="row gx-4 gy-5 mt-3">
                        <div class="col-xl-6">
                          <div class="form__group field">
                            <input
                              required
                              placeholder="{{translate('Name')}}"
                              class="form__field"
                              name="name"
                              value="{{old('name')}}"
                              type="text"
                              id="name"/>
                            <label class="form__label" for="name">
                                {{translate("Name")}}
                            </label>
                          </div>
                        </div>
                        <div class="col-xl-6">
                          <div class="form__group field">
                            <input
                              required
                              placeholder="{{translate('Phone')}}"
                              id="number"
                              class="form__field"
                              type="text"
                              name="phone"
                              value="{{old('phone')}}"/>
                            <label class="form__label" for="number">
                                {{translate("Phone")}}
                            </label>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form__group field">
                            <input
                              required
                              placeholder="{{translate('Email')}}"
                              class="form__field"
                              type="email"
                              name="email"
                              value="{{old('email')}}"
                              id="email"/>
                            <label class="form__label" for="email">
                                {{translate('Email')}}
                            </label>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form__group field">
                            <input
                              required
                              placeholder="{{translate('Subject')}}"
                              name="subject"
                              class="form__field"
                              value="{{old('subject')}}"
                              type="text"
                              id="subject"
                            />
                            <label class="form__label" for="subject">
                              {{translate("Subject")}}
                            </label>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form__group field">
                            <textarea placeholder="{{translate('Message')}}" required  class="form__field" id="message" name="message">{{old('message')}}</textarea>
                              <label class="form__label" for="message">
                                {{translate("Write your Message")}}
                              </label>
                          </div>
                        </div>
                        <div class="col-12">
                          <button  class="i-btn btn--primary btn--lg capsuled">
                                {{translate("Send Message")}}
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

