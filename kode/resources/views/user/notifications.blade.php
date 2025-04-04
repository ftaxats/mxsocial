@extends('layouts.master')
@section('content')
<div class="i-card-md">
    <div class="card-header">
        <h4 class="card-title">
            {{translate("All Notifications")}}
        </h4>
    </div>
    <div class="card-body">
        @if($notifications->count() > 0)
            <div class="notification-list list-group gap-2">
                @php
                    $user = auth_user('web')->load(['file']);
                @endphp
                @foreach($notifications as $notification)
                    @if($notification->url)
                        <a href="javascript:void(0);" data-id="{{$notification->id}}" data-href= "{{($notification->url)}}" class="read-notification list-group-item list-group-item-action ">
                            <div class="d-flex mb-2 align-items-center">
                                <div class="flex-shrink-0">
                                    <img class="rounded-circle avatar-md"
                                    src='{{imageURL($user->file,"profile,user",true) }}'
                                    alt="{{@$user->file->name ?? 'profile.jpg'}}" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="list-title">
                                        {{$user->name}}
                                    </h6>
                                    <small>
                                        {{diff_for_humans($notification->created_at)}}
                                    </small>
                                    <p class="list-text">{{strip_tags($notification->message)}}</p>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @else
           <div>
               @include('admin.partials.not_found')
           </div>
        @endif
    </div>
</div>
<div class="Paginations">
    {{ $notifications->links() }}
</div>
@endsection





