@extends('layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/viewbox/viewbox.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

@php
     $account = $post?->account;
     $platform = $account?->platform;
@endphp
    <div class="row gy-5">

      

        <div class="col-xl-8 col-lg-7">
            <div class="row g-4">
                <div class="col-xl-12">
                    <div class="i-card-md">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ translate('Basic Information') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <ul class="post-detail-list list-group list-group-flush">
                                <li class="list-group-item">
                                    <h6 class="title">{{ translate('Platform') }}</h6>
                                    <p class="value">{{ @$post->account?->platform->name ?? "--"}}</p>
                                </li>

                                <li class="list-group-item">
                                    <h6 class="title">
                                        {{ translate('Account')}}
                                    </h6>

                                    <div class="value">
                                        @if(@$post->account->account_information->link)
                                            <a class="text--primary" target="_blank" href="{{@$post->account->account_information->link}}">
                                                {{ @$post->account->account_information->name}}
                                            </a>
                                        @else
                                            {{ @$post->account->account_information->name}}
                                        @endif
                                    </div>
                                </li>

                                <li class="list-group-item">
                                    <h6 class="title">
                                        {{ translate('Schedule Time') }}
                                    </h6>

                                    <p> {{@$post->schedule_time ? get_date_time($post->schedule_time ) :'--'}}</p>
                                </li>

                                <li class="list-group-item">
                                    <h6 class="title">{{ translate('Status') }}</h6>
                                    @php echo (post_status($post->status))   @endphp
                                </li>

                                <li class="list-group-item">
                                    <h6 class="title">{{ translate('Post Type') }}</h6>
                                    @php echo (post_type($post->post_type))   @endphp
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="i-card-md">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ translate('Post Information') }}
                            </h4>
                        </div>

                        <div class="card-body">
                            <ul class="post-detail-list list-group list-group-flush">
                                <li class="list-group-item">
                                    <h6 class="title">{{ translate('Content') }}</h6>
                                    <p class="value"> {{$post->content?? '--'}}</p>
                                </li>

                                <li class="list-group-item">
                                    <h6 class="title">{{ translate('Link') }}</h6>
                                    <p class="value post__link">
                                        @if($post->link)
                                            <a target="_blank" href="{{$post->link}}">
                                                {{$post->link}}
                                            </a>
                                        @else
                                            --
                                        @endif
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-4 col-lg-5">
            <div class="row g-4">
                <div class="col-12">
                    <div class="social-preview-body post-details {{$platform->slug }}">
                        <div class="post-logo">
                            <img data-bs-toggle="tooltip" data-bs-title="{{$platform->name . translate(' Preview')}}" src="{{imageURL(@$platform->file,'platform',true)}}" alt="{{@$platform->name .translate( 'Feature image')}}">

                        </div>
                        <div class="social-auth">
                            <div class="profile-img">
                                <img src="{{get_default_img()}}" alt="{{translate('Fallback default image')}}" />
                            </div>

                            <div class="profile-meta">
                                <h6 class="user-name">
                                    {{$account? $account->name :  translate('Username')}}
                                </h6>
                                @if($platform->slug == 'facebook')
                                    <div class="d-flex align-items-center gap-2">
                                        <p>{{get_date_time($post->created_at)}}</p>
                                        <i class="bi bi-globe-americas fs-12"></i>
                                    </div>
                                @else
                                    <p>{{get_date_time($post->created_at)}}</p>
                                @endif
                            </div>

                        </div>
                        <div class="social-caption">
                            <div class="caption-text  custom-caption-text">
                                {{ $post->content??  '-'}}
                            </div>

                            @if($post->file->count() > 0)
                                @php
                                    $class = 'caption-imgs post-preview-imgs';
                                    if($post->file->count() == 1){
                                        $class.=' imgOne' ;
                                    }else if($post->file->count() == 2){
                                        $class.=' imgTwo' ;
                                    }
                                @endphp

                                <div class="{{$class}}">
                                    @foreach ($post->file->take(3) as $file)
                                        <div class="caption-img">
                                            @php
                                                $fileURL = (imageURL($file,"post",true));
                                            @endphp

                                            @if(!isValidVideoUrl($fileURL))
                                               <img src="{{$fileURL}}"  alt="{{ @$file->name }}">
                                            @else
                                                <video width="150" controls>
                                                    <source src="{{$fileURL}}">
                                                </video>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="@if($platform->slug == 'instagram') caption-action pt-3 d-flex justify-content-between align-items-center @else caption-action @endif ">
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
                                        <i class="bi bi-recycle"></i>
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
                                        <i class="bi bi-recycle"></i>
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

                @if($post->file->count() > 0)
                    <div class="col-12">
                        <div class="i-card-md">
                            <div class="card-header">
                                <h4 class="card-title">
                                    {{ translate('Post Images') }}
                                </h4>
                            </div>

                            <div class="card-body">
                            @if($post->file->count() > 0)
                                <li>
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        @foreach ($post->file as $file)
                                            <div class="d-flex">
                                                @php
                                                   $fileURL = (imageURL($file,"post",true));
                                                @endphp
                                                @if(!isValidVideoUrl($fileURL))
                                                    <div class="image-v-preview">
                                                        <img src="{{$fileURL}}" alt="{{ translate('Post preview image')}}">
                                                    </div>
                                                @else
                                                    <video  width="150" controls>
                                                        <source src="{{$fileURL}}">
                                                    </video>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </li>
                            @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>



    </div>

@endsection

@push('script-include')
      <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/viewbox/jquery.viewbox.min.js')}}"></script>
@endpush

