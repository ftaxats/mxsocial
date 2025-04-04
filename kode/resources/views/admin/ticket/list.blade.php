@extends('admin.layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

<div class="row row-cols-xl-4 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 row-cols-1 g-3 mb-4">
    <div class="col">
        <div class="i-card-sm style-2 warning">
          <div class="card-info">
            <h3>
                {{Arr::get($counter,'pending',0)}}
            </h3>
            <h5 class="title">
               {{translate("Pending Ticket")}}
            </h5>
            <a href="{{route('admin.ticket.list',['status' => App\Enums\TicketStatus::PENDING->value])}}" class="i-btn btn--sm btn--primary-outline">
              {{translate("View All")}}
           </a>
          </div>
          <div class="icon">
            <i class="las la-comment-slash"></i>
          </div>
        </div>
    </div>
    <div class="col">
      <div class="i-card-sm style-2 danger">
        <div class="card-info">
          <h3> {{Arr::get($counter,'closed',0)}}</h3>
          <h5 class="title">
             {{translate("Closed Ticket")}}
          </h5>
          <a href="{{route('admin.ticket.list',['status' => App\Enums\TicketStatus::CLOSED->value])}}" class="i-btn btn--primary-outline btn--sm">
            {{translate("View All")}}
         </a>
        </div>
        <div class="icon">
            <i class="las la-comment"></i>
        </div>
      </div>
    </div>
    <div class="col">
        <div class="i-card-sm style-2 info">
          <div class="card-info">
            <h3>{{Arr::get($counter,'hold',0)}}</h3>
            <h5 class="title">
                {{translate("Holds Ticket")}}
            </h5>
            <a href="{{route('admin.ticket.list',['status' => App\Enums\TicketStatus::HOLD->value])}}" class="i-btn btn--primary-outline btn--sm">
               {{translate("View All")}}
            </a>
          </div>
          <div class="icon">
            <i class="las la-sms"></i>
          </div>
        </div>
    </div>
    <div class="col">
      <div class="i-card-sm style-2 success">
        <div class="card-info">
          <h3>{{Arr::get($counter,'solved',0)}}</h3>
          <h5 class="title">
             {{translate("Solved Ticket")}}
          </h5>
          <a href="{{route('admin.ticket.list',['status' => App\Enums\TicketStatus::SOLVED->value])}}" class="i-btn btn--primary-outline btn--sm">
             {{translate("View All")}}
          </a>
        </div>
        <div class="icon">
            <i class="las la-envelope-open"></i>
        </div>
      </div>
    </div>
</div>
<div class="i-card-md">
    <div class="card-body">
        <div class="search-action-area">
            <div class="row g-3">
                @if(check_permission('create_category'))
                    <div class="col-md-6 col-6 d-flex justify-content-start">
                        @if(check_permission('create_category'))
                            <div class="action">
                                <a href="{{route('admin.ticket.create')}}" class="i-btn btn--sm success">
                                    <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="col-md-6 d-flex justify-content-end">
                    <div class="filter-wrapper">
                        <button class="i-btn btn--primary btn--sm filter-btn" type="button">
                            <i class="las la-filter"></i>
                        </button>


                        <div class="filter-dropdown">
                            <form action="{{ route(Route::currentRouteName()) }}" method="get">
                                <div class="form-inner">
                                    <input type="text" id="datePicker" name="date" value="{{ request()->input('date') }}" placeholder="{{ translate('Filter by date') }}">
                                </div>
                                <div class="form-inner">
                                    <input type="text" name="ticket_number" value="{{ request()->input('ticket_number') }}" placeholder="{{ translate('Enter Ticket Number') }}">
                                </div>
                                <div class="form-inner">
                                    <select name="user" id="user" class="user">
                                        <option value="">{{ translate('Select User') }}</option>
                                        @foreach(system_users() as $user)
                                            <option {{ Arr::get($user, 'username', null) == request()->input('user') ? 'selected' : '' }} value="{{ Arr::get($user, 'username', null) }}">
                                                {{ Arr::get($user, 'name', null) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inner">
                                    <select name="status" class="select2" id="status">
                                        <option value="">{{ translate('Select Status') }}</option>
                                        @foreach(App\Enums\TicketStatus::toArray() as $k => $v)
                                            <option {{ request()->input('status') == $v ? 'selected' : '' }} value="{{ $v }}">
                                                {{ ucfirst(strtolower($k)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inner">
                                    <select name="priority" class="select-priority">
                                        <option value="">{{ translate('Select Priority') }}</option>
                                        @foreach(App\Enums\PriorityStatus::toArray() as $k => $v)
                                            <option {{ request()->input('priority') == $v ? 'selected' : '' }} value="{{ $v }}">
                                                {{ ucfirst(strtolower($k)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="i-btn btn--md info w-100">
                                    <i class="las la-sliders-h"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="ms-3">
                        <a href="{{ route('admin.ticket.list') }}" class="i-btn btn--sm danger">
                                <i class="las la-sync"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-container position-relative">
            @include('admin.partials.loader')
            <table >
                <thead>
                    <tr>
                        <th scope="col">
                           #
                        </th>
                       <th scope="col">
                            {{translate("Ticket Number")}}
                       </th>
                       <th scope="col">
                            {{translate("User")}}
                       </th>
                       <th scope="col">
                           {{translate("Subject")}}
                       </th>
                       <th scope="col">
                           {{translate("Status")}}
                       </th>
                       <th scope="col">
                           {{translate("Priority")}}
                       </th>
                       <th scope="col">
                           {{translate("Creation Time")}}
                       </th>
                       <th scope="col">
                           {{translate("Options")}}
                       </th>
                   </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                            <tr>
                                <td data-label="#">

                                    {{$loop->iteration}}
                                </td>
                                <td data-label="{{translate('Ticket Number')}}">
                                    <a href="{{route('admin.ticket.show',$ticket->ticket_number)}}">
                                        {{$ticket->ticket_number}}
                                    </a>
                                </td>
                                <td data-label="{{translate('User')}}">
                                    <a href="{{route('admin.user.show', $ticket->user->uid)}}">
                                       {{$ticket->user?->name}}
                                    </a>
                                </td>
                                <td data-label="{{translate('Subject')}}">
                                    {{limit_words($ticket->subject,15)}}
                                </td>
                                <td data-label="{{translate('Status')}}">
                                    @php echo ticket_status($ticket->status) @endphp

                                </td>
                                <td data-label="{{translate('Priority')}}">
                                    @php echo priority_status($ticket->priority) @endphp
                                </td>
                                <td data-label="{{translate('Creation Time')}}">
                                    {{get_date_time($ticket->created_at)}}
                                </td>
                                <td data-label="{{translate('Options')}}">
                                    <div class="table-action">
                                        <a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Show')}}"   href="{{route('admin.ticket.show',[$ticket->ticket_number])}}"  class="icon-btn success"><i class="las la-eye"></i></a>
                                        @if(check_permission('delete_ticket') )
                                        <a  href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Delete')}}" data-href="{{route('admin.ticket.destroy',$ticket->id)}}" class="delete-item icon-btn danger">
                                            <i class="las la-trash-alt"></i></a>
                                        @endif
                                    </div>
                                </td>
                           </tr>
                        @empty
                            <tr>
                                <td class="border-bottom-0" colspan="8">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                       @endforelse
                </tbody>
            </table>
        </div>
        <div class="Paginations">
            {{ $tickets->links() }}

        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modal.delete_modal')
@endsection

@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";


        $(".select2").select2({
            placeholder:"{{translate('Select Status')}}",
        })
        $(".select-priority").select2({
            placeholder:"{{translate('Select priority')}}",
        })

        $(".user").select2({
            placeholder:"{{translate('Select User')}}",
        })
	})(jQuery);
</script>
@endpush
