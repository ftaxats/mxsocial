@php use Illuminate\Support\Arr; @endphp
@extends('layouts.master')
@section('content')

@push('style-include')
    <link nonce="{{ csp_nonce() }}"  href="{{asset('assets/frontend/css/post.css')}}" rel="stylesheet" type="text/css">
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
@php
    $user = auth_user('web')->load(['runningSubscription','runningSubscription.package']);
    $schedule = false;

    $notes = trans('default.platform_notes') ;

    if($user->runningSubscription){
        $package = $user->runningSubscription->package;
        if($package && @$package->social_access->schedule_post == App\Enums\StatusEnum::true->status()) $schedule = true;
    }
@endphp

<div class="compose-wrapper">
    <form action="{{route('user.social.post.store')}}" method="post" class="compose-form" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-xxl-8 col-lg-7">
                <div class="i-card-md">
                    <div class="card-body">
                        <div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <h4 class="card-title mb-3">{{translate('Where to post')}}</h4>
                                </div>

                                @if($platforms->count() > 0)
                                    <div class="row gy-3">
                                        <div class="col-xl-6 col-lg-12 col-md-6">
                                            <ul class="nav nav-tabs post-select-tab" id="postTypeTab" role="tablist">
                                                @foreach($platforms as  $platform)
                                                    <li class="nav-item" role="presentation">
                                                        <button  class="nav-link  {{ $loop->index == 0 ? 'active' : '' }} " id="{{$platform->slug}}-tab" data-bs-toggle="tab" data-bs-target="#{{$platform->slug}}-tab-pane" type="button" role="tab" aria-controls="{{$platform->slug}}-tab-pane" aria-selected="true">
                                                            <img src="{{imageURL(@$platform->file,'platform',true)}}" alt="{{@$platform->name .translate( 'Feature image')}}">
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="col-xl-6 col-lg-12 col-md-6 d-flex
                                                    justify-content-xl-end
                                                    justify-content-lg-start
                                                    justify-content-md-end
                                                    justify-content-start">
                                            <div class="tab-content" id="postTypeTabContent">
                                                @foreach ($platforms as  $platform)
                                                    @php

                                                        $postTypes = App\Enums\PostType::toArray();
                                                        if($platform->slug == 'facebook') $postTypes =  Arr::except( $postTypes,[App\Enums\PostType::STORY->name]);
                                                        if($platform->slug == 'twitter') $postTypes  =  Arr::except( $postTypes,[App\Enums\PostType::REELS->name,App\Enums\PostType::STORY->name]);
                                                        if($platform->slug == 'linkedin') $postTypes =  Arr::except( $postTypes,[App\Enums\PostType::REELS->name,App\Enums\PostType::STORY->name]);
                                                        if($platform->slug == 'tiktok')   $postTypes   =  Arr::except( $postTypes,[App\Enums\PostType::REELS->name,App\Enums\PostType::STORY->name]);

                                                    @endphp

                                                    <div class="tab-pane fade  {{ $loop->index == 0 ? 'show active' : '' }}" id="{{$platform->slug}}-tab-pane" role="tabpanel" aria-labelledby="{{$platform->slug}}-tab" tabindex="0">
                                                        <div class="d-flex gap-2 align-items-center">
                                                            @foreach ($postTypes as  $type => $value)
                                                                <div class="radio--button">
                                                                    <input {{ $loop->index == 0 ? 'checked' : ''}}  type="radio" id="post_type_{{$platform->slug}}-{{$loop->index}}"  name="post_type[{{$platform->slug}}]" value="{{$value}}" />
                                                                    <label for="post_type_{{$platform->slug}}-{{$loop->index}}"> {{$type}}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title mb-3">{{translate('Choose Profile')}}</h4>
                                    </div>

                                    <select name="account_id[]" multiple="multiple" class="w-100 profile-select" >
                                        @foreach (@$accounts as $account )
                                            @php
                                                $imgUrl = isValidImageUrl(@$account->account_information->avatar)
                                                                ? @$account->account_information->avatar
                                                                : route('default.image', '200x200')
                                            @endphp

                                            <option @if(old('account_id') && is_array(old('account_id')) && in_array($account->id , old('account_id')))  selected @endif    value="{{ $account ->id }}" data-image="{{ $imgUrl}}">{{$account->name}} - {{ @$account->platform->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h4 class="card-title mb-3">
                                    {{translate('Create your post')}}
                                </h4>

                                <div class="caption-wrapper">
                                    <div class="form-inner mb-0">
                                        <div class="compose-body">
                                            <textarea name="text" cols="30" rows="4"
                                                placeholder="{{translate('Start Writing')}}" class="compose-input post-intput"
                                                id="inputText" contenteditable="true">{{old('text')}}</textarea>

                                            <div class="compose-body-bottom">
                                                <div class="caption-action d-flex justify-content-start">
                                                    <div class="action-item ai-modal">
                                                        <i class="bi bi-robot"></i>
                                                        <p>
                                                            {{translate("AI Assistant")}}
                                                        </p>
                                                    </div>

                                                    <div class="upload-filed">
                                                        <input id="media-file" multiple type="file"
                                                            name="files[]">
                                                        <label for="media-file">
                                                            <span
                                                                class="d-flex align-items-center flex-row gap-2">
                                                                <span class="upload-drop-file">
                                                                   <i class="bi bi-image fs-20"></i>
                                                                </span>
                                                                <span>
                                                                    {{translate('Photo/Video')}}
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>

                                                    <div>
                                                        <select class="form-select predefined-select" aria-label="Default select example" id="predefined">
                                                            <option value="">{{translate("Predefined Content")}}</option>
                                                            @foreach($contents as  $content)
                                                                <option value="{{$content->content}}">
                                                                    {{$content->name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    @if($schedule)
                                                        <div class="schedule-btn">
                                                            <div class="px-3 custom-date-label" id="schedule_date_picker" data-bs-toggle="tooltip"
                                                                data-bs-title="{{translate('Schedule Post')}}">
                                                                <i class="bi bi-clock"></i>
                                                            </div>
                                                            <p class="show-date"></p>
                                                        </div>
                                                       <input type="datetime-local"  hidden name="schedule_date" id="schedule_date_input" >

                                                    @endif
                                                </div>
                                                <ul class="file-list mt-3"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h4 class="card-title mb-3">{{translate('Links')}}</h4>
                                <div class="input-group mb-0">
                                    <input type="text" placeholder="{{translate('Enter link')}}" name="link" id="link" value="{{old('link')}}" class="form-control" />
                                </div>
                            </div>

                            <button  type="submit"
                                class="i-btn btn--primary btn--lg capsuled postSubmitButton"
                                id="postSubmitButton">
                                {{translate("Post")}}
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4 col-lg-5">
                <div class="i-card-md social-preview-user">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{translate("Suggestion/Preview")}}
                        </h4>
                    </div>
                    <div class="card-body">

                        <div class="d-flex flex-column gap-4">
                            @foreach ($platforms as  $platform)


                                  @php
                                      $note = Arr::get($notes , $platform->slug);
                                  @endphp

                                    <div class="flip-wrapper">

                                        <div class=" platform-note post-before-social-card d-flex justify-content-start gap-3 align-items-start">
                                            <div class="icon facebook">
                                                <i class="bi bi-{{$platform->slug}}"></i>
                                            </div>
                                            <div class="content">
                                                <h5 class="mb-3">
                                                    {{
                                                        k2t($platform->slug)
                                                    }}
                                                </h5>
                                                <p>{{$note}}</p>
                                            </div>
                                        </div>

                                        <div class="social-preview-body fade-in  d-none {{$platform->slug }}">

                                            <div class="post-logo">
                                                <img data-bs-toggle="tooltip" data-bs-title="{{$platform->name . translate(' Preview')}}" src="{{e(imageURL(@$platform->file,'platform',true))}}" alt="{{@$platform->name .translate( 'Feature image')}}">
                                            </div>

                                            <div class="social-auth">
                                                <div class="profile-img">
                                                    <img src="{{e(get_default_img())}}"
                                                        alt="{{translate('Fallback default image')}}" />
                                                </div>

                                                <div class="profile-meta">
                                                    <h6 class="user-name">

                                                            {{translate('Username')}}

                                                    </h6>
                                                    @if($platform->slug == 'facebook')
                                                        <div class="d-flex align-items-center gap-2">
                                                            <p>
                                                            {{Carbon\Carbon::now()->format('F j')}}
                                                            </p>
                                                            <i class="bi bi-globe-americas fs-12"></i>
                                                        </div>
                                                    @else
                                                        <p>
                                                           {{Carbon\Carbon::now()->format('F j')}}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="social-caption">
                                                <div class="caption-text">
                                                </div>
                                                <div class="caption-imgs position-relative">
                                                    <div class="caption-img caption-placeholder">
                                                        <img class="w-100 h-100" src="{{get_default_img()}}" alt="Default Image">
                                                    </div>
                                                </div>
                                                <div class="caption-link"></div>


                                                <div class="caption-action">
                                                    @if($platform->slug == 'facebook')
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-hand-thumbs-up"></i>
                                                            <span>{{translate('Like')}}</span>
                                                        </div>

                                                        <div class="caption-action-item">
                                                            <i class="bi bi-chat-right"></i>
                                                            <span>{{translate('Comment')}}</span>
                                                        </div>

                                                        <div class="caption-action-item">
                                                            <i class="bi bi-share"></i>
                                                            <span>{{translate('Share')}}</span>
                                                        </div>
                                                    @elseif($platform->slug == 'instagram')
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-heart"></i>
                                                        </div>
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-chat-right"></i>
                                                        </div>
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-send"></i>
                                                        </div>
                                                    @elseif($platform->slug == 'twitter')
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-chat-right"></i>
                                                        </div>
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-repeat"></i>
                                                        </div>
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-heart"></i>
                                                        </div>
                                                    @elseif($platform->slug == 'linkedin')
                                                        <div class="caption-action-item">
                                                            <i class="bi bi-hand-thumbs-up"></i>
                                                            <span>{{translate('Like')}}</span>
                                                        </div>

                                                        <div class="caption-action-item">
                                                            <i class="bi bi-chat-right"></i>
                                                            <span>{{translate('Comment')}}</span>
                                                        </div>

                                                        <div class="caption-action-item">
                                                            <i class="bi bi-repeat"></i>
                                                            <span>{{translate('Repost')}}</span>
                                                        </div>

                                                        <div class="caption-action-item">
                                                            <i class="bi bi-send"></i>
                                                            <span>{{translate('Send')}}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>

                                    </div>



                            @endforeach
                        </div>

                        @if($platforms->count() == 0)
                           @include('admin.partials.not_found')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


@endsection

@section('modal')


<div class="modal fade" id="aiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="aiModal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title ai-modal-title">
                    {{translate('Generate Content')}}
                </h5>

                <button class="icon-btn icon-btn-sm danger" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="modal-body modal-body-section">
                @include('partials.prompt_content',['content_route' => route("user.ai.content.store"),'modal' => true])
            </div>
        </div>
    </div>
</div>


@endsection

@push('script-include')
     @include('partials.ai_content_script');
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/post.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
@endpush


@push('script-push')
<script nonce="{{ csp_nonce() }}">
(function($) {
    "use strict";

    $(document).on('change', '#predefined', function(e) {
        e.preventDefault()
        var value = $(this).val();
        var cleanContent = DOMPurify.sanitize(value);
        $("#inputText").val(cleanContent)
        $(".caption-text").html(cleanContent)
        $('.platform-note').addClass('d-none');
        $('.social-preview-body').removeClass('d-none');

    })

    $(".user").select2({  placeholder:"{{translate('Select user')}}"})

    $(document).on('click', '.ai-modal', function(e) {
        e.preventDefault()
        var modal = $('#aiModal');
        modal.find('.ai-content-form')[0].reset();
        modal.find('.ai-content-div').addClass("d-none")
        modal.find('#ai-form').fadeIn()
        modal.find('.ai-modal-title').html("{{translate('Generate Content')}}")
        modal.modal('show');

    });


    $(document).on('click', '.insert-text', function(e) {

        e.preventDefault()
        var content = $('textarea#content').val();
        var cleanContent = DOMPurify.sanitize(content);
        $('.post-intput').val(cleanContent)
        var modal = $('#aiModal');
        modal.modal('hide');

        $(".caption-text").html(cleanContent);

        $('.platform-note').addClass('d-none');
        $('.social-preview-body').removeClass('d-none');



    });


    $(".select2").select2({
        placeholder:"{{translate('Select Category')}}",
        dropdownParent: $("#aiModal"),
    })
    $(".language").select2({
        placeholder:"{{translate('Select Language')}}",
        dropdownParent: $("#aiModal"),
    })

    $(".selectTemplate").select2({
        placeholder:"{{translate('Select Template')}}",
        dropdownParent: $("#aiModal"),
    })
    $(".sub_category_id").select2({
        placeholder:"{{translate('Select Sub Category')}}",
        dropdownParent: $("#aiModal"),
    })

    $(document).on('click', '.copy-content', function(e) {
        e.preventDefault()
        var textarea = document.getElementById('content');
        textarea.select();
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
        toastr("{{translate('Text copied to clipboard!')}}", 'success');
    });




    $(document).on('click', '.download-text', function(e) {
        e.preventDefault()
        var content = document.getElementById('content').value;
        var cleanContent = DOMPurify.sanitize(content);

        var blob = new Blob([cleanContent], {
            type: 'text/html'
        });
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = 'downloaded_content.html';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = $(state.element).data('image');
        var $state = $( '<span class="image-option"><img src="' + baseUrl + '" class="img-flag" /> ' + $('<div>').text(state.text).html() + '</span>' );
        return $state;
    }

    $('.profile-select').select2({
        templateResult: formatState,
        templateSelection: formatState,
    });

    function execCommandWithPreventDefault(command) {
        return function(event) {
            event.preventDefault();
            document.execCommand(command, false, null);
        };
    }


    var start = null;
    var end = null;

    function cb(start, end) {
        if (start) {
            const formattedDate = start.format('YYYY-MM-DDTHH:mm');
            const humanReadableDate = start.format('MMMM D, YYYY h:mm A');
            var cleanContent = DOMPurify.sanitize(humanReadableDate);
            $('#schedule_date_input').val(formattedDate);
            $('.show-date').html(`
                    <span class="pe-3">${cleanContent}
                    <i class="bi bi-x ps-2 fs-6 text--danger pointer  clear-input "></i></span>`);

        } else {
            $('#schedule_date_input').val('');
            $('.show-date').html('');
        }
    }


        $('#schedule_date_picker').daterangepicker(
            {
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-MM-DDTHH:mm'
                }
            },
            cb
        );

        $('#schedule_date_picker').on('apply.daterangepicker', function (ev, picker) {
            cb(picker.startDate, picker.endDate);
        });

        $(document).on('click', '.clear-input',function(e){
            e.preventDefault()
            cb(null, null);
        })
        cb(start, end);


    const selectTwo = document.querySelector(".select-two");
  $(document).ready(function () {
    if (selectTwo) {
      $(selectTwo).select2(
        {
            placeholder: "Select a state",
        }
      );
    }
  });

})(jQuery);

</script>
@endpush


