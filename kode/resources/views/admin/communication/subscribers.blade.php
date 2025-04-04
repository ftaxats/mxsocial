@extends('admin.layouts.master')
@push('style-include')
  <link nonce="{{ csp_nonce() }}" rel="stylesheet" href="{{asset('assets/global/css/summnernote.css')}}">
@endpush
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.subscriber.bulk")}}' method="post">
                        @csrf
                         <input type="hidden" name="bulk_id" id="bulkid">
                         <input type="hidden" name="value" id="value">
                         <input type="hidden" name="type" id="type">
                    </form>
                    <div class="col-md-6 d-flex justify-content-start gap-2">
                        @if(check_permission('update_frontend') )
                            <div class="i-dropdown bulk-action mx-0 d-none">
                                <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="las la-cogs fs-15"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button data-type="delete"  class="dropdown-item bulk-action-modal">
                                            {{translate("Delete")}}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        @endif
                        <div class="action">
                            <button type="button"  data-bs-toggle="modal" data-bs-target="#sendMailToAll" class="i-btn btn--sm success">
                                <i class="las la-paper-plane me-1"></i>  {{translate('Send Mail')}}
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="email" value="{{request()->input('email')}}" type="search" placeholder="{{translate('Search by email')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container position-relative">
                @include('admin.partials.loader')
                @if($subscribers->count() > 0)
                    <table >
                        <thead>
                            <tr>
                                <th scope="col">
                                    @if(check_permission('update_frontend'))
                                        <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                    @endif#
                                </th>
                                <th scope="col">
                                    {{translate('Email')}}
                                </th>
                                <th scope="col">
                                    {{translate('Subscribe At')}}
                                </th>
                                <th scope="col">
                                    {{translate('Options')}}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscribers as $subscriber)
                                    <tr>
                                        <td data-label="#">
                                            @if(check_permission('update_frontend'))
                                            <input type="checkbox" value="{{$subscriber->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$subscriber->id}}" />
                                            @endif
                                            {{$loop->iteration}}
                                        </td>
                                        <td data-label='{{translate("Email")}}'>
                                            {{$subscriber->email}}
                                        </td>
                                        <td data-label='{{translate("Time")}}'>
                                            {{diff_for_humans($subscriber->created_at)}}
                                        </td>
                                        <td data-label='{{translate("Options")}}'>
                                            <div class="table-action">
                                                @if(check_permission('update_frontend'))
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Send mail')}}" href="javascript:void(0);" data-email="{{$subscriber->email}}"  class="sendMail fs-15 icon-btn info"><i class="las la-paper-plane"></i></a>
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Delete')}}" href="javascript:void(0);" data-href="{{route('admin.subscriber.destroy',$subscriber->uid)}}" class="delete-item icon-btn danger">
                                                        <i class="las la-trash-alt"></i></a>
                                                @else
                                                    --
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                        </tbody>
                    </table>
                @else
                    @include('admin.partials.not_found',['custom_message' => "No subscribers found!!"])
                @endif
            </div>
            <div class="Paginations">
                {{ $subscribers->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modal.delete_modal')
    @include('modal.bulk_modal')
      <!-- send mail modal -->
    <div class="modal fade" id="sendMailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sendMailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-icon" >
                        {{translate('Send Mail')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.send.mail')}}" id="updateModalForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="email" id="email" class="form-control" >
                        <div class="row">
                            <div class="col-12">
                                <div class="form-inner">
                                    <label for="message">
                                        {{translate('Message')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                    <textarea class="summernote" required placeholder="{{translate('Type Here')}}" name="message" id="message" cols="30" rows="5">{{old("message")}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="i-btn btn--md ripple-dark" data-anim="ripple" data-bs-dismiss="modal">
                            {{translate("Close")}}
                        </button>
                        <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sendMailToAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sendMailToAll" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-icon" >
                        {{translate('Send Mail')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.send.mail.all')}}"  method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-inner">
                                    <label for="message-all">
                                        {{translate('Message')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                    <textarea class="summernote" required placeholder="{{translate('Type Here')}}" name="message" id="message-all" cols="30" rows="5">{{old("message")}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="i-btn btn--md ripple-dark" data-anim="ripple" data-bs-dismiss="modal">
                            {{translate("Close")}}
                        </button>
                        <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/summernote.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/editor.init.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
        "use strict";

        $(document).on('click','.sendMail',function(e){
            e.preventDefault()
            var email = $(this).attr('data-email')
            var modal = $('#sendMailModal')
            modal.find('input[name="email"]').val(email)
            modal.modal('show')
        })

	})(jQuery);
</script>
@endpush



