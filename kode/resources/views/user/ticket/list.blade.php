@extends('layouts.master')
@push('style-include')
      <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
    <div>
        <div class="w-100 d-flex align-items-center justify-content-between gap-lg-5 gap-3 flex-md-nowrap flex-wrap mb-4">
                <h4>
                    {{translate(Arr::get($meta_data,'title'))}}
                </h4>

                <div class="d-flex align-items-center gap-2">
                    <button class="icon-btn icon-btn-lg bg-info-solid text--light circle" type="button" data-bs-toggle="collapse" data-bs-target="#tableFilter"     aria-expanded="false"
                        aria-controls="tableFilter">
                        <i class="bi bi-sliders text-white"></i>
                    </button>
                    <a href="{{route('user.ticket.create')}}" class="i-btn primary btn--md capsuled">
                        <i class="bi bi-plus-lg"></i>
                        {{translate('Create Ticket')}}
                    </a>
                </div>
        </div>



        <div class="collapse {{ hasFilter(['date','ticket_number','priority','status']) ? 'show' : '' }}  " id="tableFilter">
            <div class="search-action-area mb-4">
                  <div class="search-area">
                        <form action="{{ route(Route::currentRouteName()) }}" method="get">
                            <div class="form-inner">
                                <input type="text" id="datePicker" name="date" value="{{ request()->input('date') }}" placeholder="{{ translate('Filter by date') }}">
                            </div>

                            <div class="form-inner">
                                <input type="text" name="ticket_number" value="{{ request()->input('ticket_number') }}" placeholder="{{ translate('Enter Ticket Number') }}">
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

                            <div class="d-flex gap-2">
                                <button class="i-btn primary btn--lg capsuled">
                                    <i class="bi bi-search"></i>
                                </button>
                                <a href="{{ route('user.ticket.list') }}" class="i-btn danger btn--lg capsuled">
                                    <i class="bi bi-arrow-repeat"></i>
                                </a>
                            </div>
                        </form>
                  </div>
            </div>
        </div>

        <div class="i-card-md">
            <div class="card-body">
                @if($tickets->count() > 0)
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{translate("Ticket Number")}}</th>
                                    <th scope="col">{{translate("Subject")}}</th>
                                    <th scope="col">{{translate("Status")}}</th>
                                    <th scope="col">{{translate("Priority")}}</th>
                                    <th scope="col">{{translate("Creation Time")}}</th>
                                    <th scope="col">{{translate("Options")}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td data-label="#">{{$loop->iteration}}</td>
                                        <td data-label="{{translate('Ticket Number')}}">
                                            <a href="{{route('user.ticket.show',$ticket->ticket_number)}}">
                                                {{$ticket->ticket_number}}
                                            </a>
                                        </td>
                                        <td data-label="{{translate('Subject')}}">
                                            {{limit_words($ticket->subject,15)}}
                                        </td>
                                        <td data-label="{{translate('Status')}}">
                                            @php echo (ticket_status($ticket->status)) @endphp
                                        </td>
                                        <td data-label="{{translate('Priority')}}">
                                            @php echo (priority_status($ticket->priority)) @endphp
                                        </td>
                                        <td data-label="{{translate('Creation Time')}}">
                                            {{get_date_time($ticket->created_at)}}
                                        </td>
                                        <td data-label="{{translate('Options')}}">
                                            <div class="table-action">
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Show')}}"
                                                    href="{{route('user.ticket.show',[$ticket->ticket_number])}}"
                                                    class="icon-btn icon-btn-sm info">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}"  href="javascript:void(0);" data-href="{{route('user.ticket.destroy',$ticket->id)}}" data-toggle="tooltip" data-placement="top" title="{{translate('Delete')}}"
                                                    class="icon-btn icon-btn-sm danger delete-item">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                   @include('admin.partials.not_found',['custom_message' => 'No tickets found'])
                @endif
            </div>
        </div>
        <div class="Paginations">
            {{ $tickets->links() }}
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
	})(jQuery);
</script>
@endpush

