@extends('admin.layouts.master')

@section('content')
    <div class="basic-setting">
        <div class="basic-setting-left">
            <div class="setting-tab sticky-side-div">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="v-pills-basic-settings-tab" data-bs-toggle="tab" href="#v-pills-basic-settings" role="tab" aria-controls="v-pills-basic-settings" aria-selected="false" tabindex="-1">
                            <i class="las la-cog"></i> {{translate('Basic Configuration')}}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link " id="v-pills-secret-tab" data-bs-toggle="tab" href="#v-pills-secret" role="tab" aria-controls="v-pills-secret" aria-selected="false" tabindex="-1">
                            <i class="las la-key"></i> {{translate('Open AI Secret Key')}}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="basic-setting-right">
            <div id="myTabContent2" class="tab-content">
                <div class="tab-pane fade active show" id="v-pills-basic-settings" role="tabpanel" aria-labelledby="v-pills-basic-settings-tab">
                    <form class="settingsForm"   enctype="multipart/form-data">
                        @csrf
                        <div class="i-card-md">
                            <div class="card--header">
                                <h4 class="card-title">
                                     {{translate('Basic Settings')}}
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="open_ai_model"
                                                class="form-label">{{ translate('Open AI Model') }}
                                                <small class="text-danger" >*</small>
                                            </label>
                                            <select class="select2" id="open_ai_model" name="site_settings[open_ai_model]"
                                                required>
                                               @foreach (Arr::get(config('settings'),'open_ai_model',[]) as $k => $v )
                                                <option value="{{$k}}" {{site_settings("open_ai_model") == $k  ?"selected" :""}} >
                                                    {{ $v }}
                                                </option>
                                               @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                           <label for="ai_default_creativity"
                                            class="form-label">{{ translate('Default Creativity Level') }}
                                            <small class="text-danger" >*</small></label>
                                            <select class="select2" id="ai_default_creativity" name="site_settings[ai_default_creativity]" required>
                                               @foreach (Arr::get(config('settings'),'default_creativity',[]) as $k => $v )
                                                    <option value="{{$v}}" {{site_settings("ai_default_creativity") == $v
                                                    ? "selected" :""}} >
                                                        {{ $k }}
                                                    </option>
                                               @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="ai_default_tone"
                                                class="form-label">{{ translate('Default Tone') }}
                                                <small class="text-danger" >*</small>
                                            </label>
                                            <select class="select2" id="ai_default_tone" name="site_settings[ai_default_tone]" required>
                                                   @foreach (Arr::get(config('settings'),'ai_default_tone',[]) as $v )
                                                        <option value="{{$v}}" {{site_settings("ai_default_tone") == $v  ? "selected" :""}} >
                                                            {{ $v }}
                                                        </option>
                                                   @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="default_max_result"
                                            class="form-label">{{ translate('Default Max Result Length') }}
                                                <small class="text-danger" >*</small>
                                                <i data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Insert -1 to make it unlimited')}}" class="ms-1  pointer las la-question-circle  text--danger"></i>
                                            </label>
                                            <input placeholder='{{translate("Max Result")}}' type="number" id="default_max_result" name="site_settings[default_max_result]"
                                                class="form-control" value='{{ site_settings("default_max_result") }}'
                                                min="-1">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-inner">
                                            <label for="ai_bad_words"
                                                class="form-label">{{ translate('Bad Words') }}
                                                <small class="text-danger">*</small>
                                                <i  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('These words will be filtered from user inputs while generating contents')}}" class="text--danger pointer ms-1 las la-question-circle"></i>
                                            </label>
                                            <textarea placeholder="{{translate('Enter words')}}"  name="site_settings[ai_bad_words]" id="ai_bad_words" cols="30" rows="2">{{site_settings("ai_bad_words")}}</textarea>
                                            <small>
                                                {{translate("Comma Separated: One, Two")}}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-12 ">
                                        <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                                            {{translate("Submit")}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="v-pills-secret" role="tabpanel" aria-labelledby="v-pills-secret-tab">
                    <form   class="settingsForm"   enctype="multipart/form-data">
                        @csrf
                        <div class="i-card-md">
                            <div class="card--header ">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <h4 class="card-title">
                                        {{translate('Secret Key Settings')}}
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-20 add-key-btn @if(site_settings('ai_key_usage') == App\Enums\StatusEnum::true->status()) d-none @endif">
                                        <a href="javascript:void(0)" class="i-btn btn--md success" id="addNew">  <i class="las la-plus me-1"></i> {{translate('Add New Key')}}</a>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="@if(site_settings('ai_key_usage') == App\Enums\StatusEnum::false->status())  addedField @endif form-inner api-key-section">
                                            <div class="main-api-key @if(site_settings('ai_key_usage') == App\Enums\StatusEnum::false->status()) d-none @endif">
                                                <label for="open_ai_secret"
                                                class="form-label">{{ translate('Open AI Secret Key') }}
                                                    <small class="text-danger" >*</small>
                                                </label>
                                                <input placeholder="{{translate('Open AI Secret Key')}}" type="text" id="open_ai_secret" name="site_settings[open_ai_secret]"
                                                    class="form-control" value="{{ is_demo() ? '@@@' :site_settings('open_ai_secret') }}" >
                                            </div>
                                            <div class="random-keys  @if(site_settings('ai_key_usage') == App\Enums\StatusEnum::true->status()) d-none @endif">
                                                @foreach (format_rand_keys() as $k => $v )
                                                    <div class="form-group mb-10">
                                                        <div class="input-group">
                                                            <input name="site_settings[rand_api_key][keys][]" class="form-control" type="text" value="{{is_demo() ? '@@@' :$k}}" required placeholder="{{translate('Api key')}}">
                                                            <select name="site_settings[rand_api_key][status][]" required  class="form-control ms-3">
                                                                @foreach (App\Enums\StatusEnum::toArray() as $key => $val )
                                                                    <option value="{{$val}}" {{$v == $val  ? "selected" :""}} >
                                                                        {{ $key }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span class="input-group-text pointer delete-option">
                                                                <i class="las  la-times-circle"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-inner">
                                            <label for="ai_key_usage"
                                            class="form-label">{{ translate('Open AI Key Usage') }}
                                                <small class="text-danger" >*</small>
                                            </label>
                                            <select class="select2 api-key-usage" id="ai_key_usage" name="site_settings[ai_key_usage]"
                                                    required>
                                                @foreach (App\Enums\StatusEnum::toArray() as $k => $v )
                                                    <option value="{{$v}}" {{site_settings("ai_key_usage") == $v  ? "selected" :""}} >
                                                        {{ $v == 1 ? translate("Main API key") : 'Random API Key'  }}
                                                    </option>
                                                @endforeach
                                           </select>
                                        </div>
                                    </div>
                                    <div class="col-12 ">
                                        <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                                            {{translate("Submit")}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
  "use strict";

    $(".select2").select2({
            placeholder:"{{translate('Select Option')}}",
    })

    $(document).on('change',".api-key-usage",function(e){

        check_key_usage($(this).val())

    })

    function check_key_usage(val){

        if(val == 1){
            $('.random-keys').addClass("d-none");
            $('.main-api-key').removeClass("d-none")
            $('.add-key-btn').addClass("d-none");
            $('.api-key-section').removeClass("addedField");

        }
        else{
            $('.random-keys').removeClass("d-none");
            $('.main-api-key').addClass("d-none");
            $('.add-key-btn').removeClass("d-none");
            $('.api-key-section').addClass("addedField");

        }

    }

    $(document).on('click','#addNew',function (e) {

        e.preventDefault()
        var inputs = `<div class="form-group mb-10">
                        <div class="input-group">
                            <input name="site_settings[rand_api_key][keys][]" class="form-control" type="text" value="" required placeholder="{{translate('Api key')}}">

                            <select name="site_settings[rand_api_key][status][]" required  class="form-control">
                                <option value="1">{{translate('Active')}}</option>
                                <option value="0">{{translate('Inactive')}}</option>
                            </select>

                            <span class="input-group-text pointer delete-option  ">
                                    <i class="las  la-times-circle"></i>
                            </span>
                        </div>
                    </div>`;
        $('.random-keys').append(inputs)
    });


    $(document).on('click', '.delete-option', function (e) {

        e.preventDefault()
        $(this).closest('.input-group').parent().remove();
    });

</script>
@endpush
