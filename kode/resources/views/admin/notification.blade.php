@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{translate("All Notifications")}}
            </h4>
        </div>
        <div class="card-body"> 
            @if($notifications->count() > 0)
                <div class="list-group">
                    @foreach($notifications as $notification)
                        @if($notification->url)
                            <a href="javascript:void(0);" data-id="{{$notification->id}}" data-href= "{{($notification->url)}}"   class="read-notification list-group-item list-group-item-action ">
                                <div class="d-flex mb-2 align-items-center">
                                    <div class="flex-shrink-0">
                                        <img class="rounded-circle avatar-sm"
                                        src='{{imageURL(auth_user()->file,"profile,admin",true) }}'
                                        alt="{{@auth_user()->file->name ?? 'profile.jpg'}}" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="list-title"> 
                                            {{auth_user()->name}}
                                        </h6>
                                        <p class="list-text"> {{diff_for_humans($notification->created_at)}}</p>
                                    </div>
                                </div>
                                <p class="list-text">    
                                    {{strip_tags($notification->message)}}
                                </p>
                            </a>
                        @endif
                    @endforeach
                </div>
                <div class="Paginations">
                    {{ $notifications->links() }}
                </div>
            @else
                <div>
                    @include('admin.partials.not_found')
                </div>
             @endif
        </div>
    </div>

@endsection
   





