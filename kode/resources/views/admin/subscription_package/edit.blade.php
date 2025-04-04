@extends('admin.layouts.master')

@push('style-include')
    <link  nonce="{{ csp_nonce() }}"rel="stylesheet" href="{{asset('assets/global/css/bootstrapicons-iconpicker.css')}}">
@endpush

@section('content')
    <form action="{{route('admin.subscription.package.update')}}" class="add-listing-form" enctype="multipart/form-data" novalidate method="post">
        @csrf
        <input hidden type="text" name="id" value="{{$package->id}}">
        <div class="i-card-md">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-inner">
                            <label for="title">
                                {{translate('Title')}} <small class="text-danger">*</small>
                            </label>
                            <input  placeholder="{{translate('Enter Title')}}" id="title"  required type="text" name="title" value="{{$package->title}}">
                        </div>
                    </div>


                    <div class="col-lg-4">
                        <div class="form-inner">
                            <label for="Icon">
                                {{translate('Icon')}} <span class="text-danger">*</span>
                            </label>
                            <input placeholder='{{translate("Search Icon")}}' class="icon-picker" value='{{$package->icon}}' type="text" name="icon" id="Icon">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label for="duration">
                            {{translate('Duration')}} <small class="text-danger">*</small>
                        </label>
                        <select id="duration" required name="duration" class="select2" >
                            @foreach( App\Enums\PlanDuration::toArray() as $key => $val)
                                <option {{ $package->duration ==  $val ? 'selected' :""}}  value="{{$val}}">
                                    {{ucfirst(strtolower(str_replace("_"," ",$key)))}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="price">
                                {{translate('Price')}} <small class="text-danger">*</small>
                            </label>
                            <div class="input-group mb-3">
                                <input placeholder="{{translate('Enter Price')}}" id="price" step="any" required type="number" min="0" name="price" value="{{$package->price}}" class="form-control">
                                <span class="input-group-text"> {{(base_currency()->code)}} </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="discount_price">
                                {{translate(' Discount Price')}}
                            </label>
                            <div class="input-group mb-3">
                                <input class="form-control" id="discount_price" placeholder="{{translate('Enter Discount Price')}}" step="0.1" type="number" min="0" name="discount_price" value="{{$package->discount_price}}">

                                <span class="input-group-text"> {{(base_currency()->code)}} </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="affiliate_commission">
                                {{translate('Affiliate Commission')}}
                            </label>
                            <div class="input-group mb-3">
                                <input class="form-control" id="affiliate_commission" placeholder="{{translate('Enter commission')}}" step="0.1" type="number" min="0" max="100" name="affiliate_commission" value="{{$package->affiliate_commission}}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="open_ai_model">
                                {{translate("Template Access")}}
                            </label>

                            <select class="select-template" name="template_access[]" multiple="multiple">
                                <option value="">
                                    {{translate('Select Template')}}
                                </option>

                                @foreach ($templates as $id => $name)
                                    <option    selected value="{{$id }}">
                                        {{$name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-inner">
                            <label for="description">
                                {{translate('Description')}} <small class="text-danger">*</small>
                            </label>
                            <textarea required placeholder="{{translate('Enter Description')}}" name="description" id="description"  cols="30" rows="5">{{$package->description}}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3 mb-3">
                        <div class="faq-wrap style-2">
                            <div class="accordion" id="advanceOption">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="socailConfig">
                                        <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#socailSection"
                                        aria-expanded="true"
                                        aria-controls="socailSection">
                                            {{translate("Platform Configuration")}}

                                            <i data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Social Platform Configuration')}}" class="ms-1  pointer las la-question-circle  text--danger"></i>
                                        </button>
                                    </h2>
                                    <div
                                        id="socailSection"
                                        class="accordion-collapse collapse show"
                                        aria-labelledby="socailConfig"
                                        data-bs-parent="#advanceOption">
                                        <div class="accordion-body">
                                            <div class="row align-items-center">
                                                <div class="col-xl-6">
                                                    <div class="form-inner">
                                                        <label for="platform_access">
                                                            {{translate("Platform Access")}} <small class="text-danger" >*</small>
                                                        </label>
                                                        <select required multiple class="select2" id="platform_access" name="social_access[platform_access][]" >
                                                            <option  value="">
                                                                {{translate("Select Platform")}}
                                                            </option>
                                                            @foreach ($platforms as $platform )
                                                                <option {{ in_array($platform->id , @$package->social_access->platform_access ?? []  ) ? 'selected' :""    }}  value="{{$platform->id}}" >
                                                                    {{ $platform->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="form-inner">
                                                        <label for="profile"
                                                        class="form-label">{{ translate('Total Profile') }}
                                                        <small class="text-danger" >*</small></label>
                                                        <input type="number" min="1"
                                                        placeholder="{{translate('Total Profile')}}"
                                                        value="{{@$package->social_access->profile}}" name="social_access[profile]" id="profile" required>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div class="form-inner mb-0">
                                                        <label for="post"
                                                        class="form-label">{{ translate('Total Post') }}
                                                        <small class="text-danger" >*</small>

                                                        <i data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Set -1 make to it unlimited')}}" class="ms-1  pointer las la-question-circle  text--danger"></i>
                                                        </label>

                                                        <input type="number" min="-1"
                                                        value="{{@$package->social_access->post}}" name="social_access[post]" id="post" placeholder="{{translate('Total Post')}}" required   >
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <div>
                                                        <label class="form-label lh-1">
                                                            {{ translate('Webhook & Schedule') }}
                                                        </label>

                                                        <div class="d-flex align-items-center flex-wrap gap-4 border py-2 px-3 rounded-2">
                                                            <div class="d-flex align-items-center gap-2 mb-1 pointer">
                                                                <input @if(@$package->social_access->webhook_access && $package->social_access->webhook_access == App\Enums\StatusEnum::true->status() ) checked  @endif   id="webhook_access" value="{{App\Enums\StatusEnum::true->status()}}"  class="form-check-input" name="social_access[webhook_access]" type="checkbox">
                                                                <label for="webhook_access" class="form-check-label me-3 mb-0">
                                                                    {{translate('Webhook Access')}}
                                                                </label>
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2 mb-1 pointer">
                                                                <input @if(@$package->social_access->schedule_post && $package->social_access->schedule_post == App\Enums\StatusEnum::true->status() ) checked  @endif  id="schedule_post" value="{{App\Enums\StatusEnum::true->status()}}"  class="form-check-input" name="social_access[schedule_post]" type="checkbox">
                                                                <label for="schedule_post" class="form-check-label me-3 mb-0">
                                                                    {{translate('Schedule Posting')}}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="aiConfig">
                                        <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#aiSection"
                                        aria-expanded="true"
                                        aria-controls="aiSection">
                                            {{translate("AI Configuration")}}

                                            <i data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Configure AI settings that package should include')}}" class="ms-1  pointer las la-question-circle  text--danger"></i>
                                        </button>
                                    </h2>
                                    <div
                                        id="aiSection"
                                        class="accordion-collapse collapse "
                                        aria-labelledby="aiConfig"
                                        data-bs-parent="#advanceOption">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-inner mb-0">
                                                        <label for="open_ai_model">
                                                            {{translate("AI Model")}}
                                                        </label>

                                                        <select   class="form-select" id="open_ai_model" name="ai_configuration[open_ai_model]" >
                                                            <option  value="">
                                                                {{translate("Select Model")}}
                                                            </option>
                                                            @foreach (Arr::get(config('settings'),'open_ai_model',[]) as $k => $v )
                                                                <option value="{{$k}}" {{@$package->ai_configuration->open_ai_model == $k  ? "selected" :""}} >
                                                                    {{ $v }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-inner mb-0">
                                                        <label for="word_limit" >{{ translate('No. Of Words') }}
                                                            <small class="text-danger" >*</small>
                                                            <i data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Set -1 make to it unlimited')}}" class="ms-1  pointer las la-question-circle  text--danger"></i>
                                                        </label>
                                                        <input type="number" min="-1"
                                                        value="{{@$package->ai_configuration->word_limit}}" name="ai_configuration[word_limit]" id="word_limit" placeholder="{{translate('No. of Words')}}"   >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
   </form>
@endsection

@push('script-include')
        <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/bootstrapicon-iconpicker.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

            $(".select2").select2({
			   placeholder:"{{translate('Select Item')}}",
	     	})

             $('.icon-picker').iconpicker({
                   title: "{{translate('Search Here !!')}}",
             });


            $(`.select-template`).select2({
                placeholder:"{{translate('Select Template')}}",
                allowClear: false,
                tags: true,
                ajax: {
                    url: "{{route('admin.subscription.package.selectSearch')}}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
	})(jQuery);
</script>
@endpush
