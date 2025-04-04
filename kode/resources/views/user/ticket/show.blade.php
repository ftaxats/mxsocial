@extends('layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" rel="stylesheet" href="{{ asset('assets/global/css/summnernote.css') }}">
@endpush
@section('content')

<div class="row g-4 mb-4">
    <div class="col-xxl-8 col-lg-7">
        <div class="i-card-md">
            <div class="card-header">
                <h4 class="card-title">
                    {{translate(Arr::get($meta_data,'title'))}}
                </h4>
            </div>

            <div class="card-body">
                <div class="ticket-conversation">
                    @if ($ticket->status == App\Enums\TicketStatus::CLOSED->value)
                        <div class="bg-light rounded-4 py-4 text-center overflow-hidden">
                            <i class="bi bi-x-circle fs-1 text-danger"></i>
                            <p class="fw-medium fs-5">
                                {{ translate('Ticket Closed') }}
                            </p>
                        </div>
                    @else
                        <form action="{{ route('user.ticket.reply') }}" class="give-replay" method="post">
                            @csrf
                            <input hidden value="{{ $ticket->id }}" type="text" name="id">
                            <textarea class="summernote" name="message" rows="3"
                                placeholder="{{ translate('Reply Here ....') }}"></textarea>
                            <div class="give-replay-action mt-4">
                                <div>
                                    <button class="post-replay i-btn btn--primary btn--lg capsuled" type="submit">
                                        {{ translate('Submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                    @php
                        $messages = $ticket->messages;
                        $files = $ticket->file;
                    @endphp
                </div>

                <div class="discussion-continer mt-5">
                    <div class="i-card-md">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{ translate('Ticket Message') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="message-wrapper" data-simplebar>
                                @php
                                     $userImage = imageURL(@$ticket->user->file,"profile,user",true);
                                @endphp
                                @forelse($messages  as $message)
                                        @php
                                            if ($message->admin_id) $imgURL = imageURL($message->admin->file,"profile,admin",true);
                                        @endphp
                                        <div class="message-single @if ($message->admin_id) message-left @else message-right  @endif d-flex flex-column">
                                            <div class="user-area d-inline-flex @if (!$message->admin_id) justify-content-end @endif align-items-center gap-3 mb-2">
                                                @if ($message->admin_id)
                                                    <div class="image">
                                                        <img src="{{imageURL($message->admin->file,'profile,admin',true)}}" alt="{{translate('User profile image')}}">
                                                    </div>
                                                    <div class="meta">
                                                        <h6> {{ $message->admin?->name }}</h6>
                                                    </div>
                                                @else
                                                    <div class="meta">
                                                        <h6> {{ translate("Me") }} </h6>
                                                    </div>
                                                    <div class="image">
                                                        <img src="{{$userImage}}" alt="{{translate('User profile image')}}">
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="message-body">
                                                 @php echo ($message->message)  @endphp
                                                <div class="message-time">
                                                    <span> {{ diff_for_humans($message->created_at) }} </span>
                                                </div>
                                            </div>
                                        </div>
                                @empty
                                   @include('admin.partials.not_found')
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-4 col-lg-5">
        <div class="i-card-md">
            <div class="card-header">
                <h4 class="card-title">
                    {{ translate('Ticket Details') }}
                </h4>
            </div>

            <div class="card-body">
                <div class="ticket-dtable">
                    <table>
                        <tbody>
                            <tr>
                                <td>{{ translate('Ticket Id') }} </td>
                                <td>
                                    {{ $ticket->ticket_number }}
                                </td>
                            </tr>

                            <tr>
                                <td>{{ translate('Subject') }} </td>
                                <td>
                                    {{ $ticket->subject }}
                                </td>
                            </tr>

                            <tr>
                                <td> {{ translate('Date') }} </td>
                                <td id="c-date"> {{ get_date_time($ticket->created_at) }}</td>
                            </tr>

                            <tr>
                                <td>{{ translate('Status') }} </td>
                                <td>
                                    @php echo (ticket_status($ticket->status)) @endphp
                                </td>
                            </tr>

                            <tr>
                                <td>{{ translate('Priority') }} </td>
                                <td>
                                    @php echo (priority_status($ticket->priority)) @endphp
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="i-card-md mt-4">
            <div class="card-header">
                <h4 class="card-title">
                    {{ translate('Custom Data') }}
                </h4>
            </div>

            <div class="card-body">
                <div class="ticket-dtable">
                    <table>
                        <tbody>
                            @forelse($ticket->ticket_data as $k => $v)
                                @if ($k != 'description')
                                    <tr>
                                        <td>{{ ucfirst($k) }} :</td>
                                        <td>
                                            {{ $v }}
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td class="border-bottom-0" colspan="2">
                                        @include('admin.partials.not_found')
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($files && $files->count() > 0)
            <div class="i-card-md mt-4">
                <div class="card-header">
                    <h4 class="card-title">
                        {{ translate('Files') }}
                    </h4>
                </div>

                <div class="card-body">
                    <div class="ticket-attach">
                        @foreach ($files as $file)
                            <form action="{{ route('user.ticket.file.download') }}" method="post">
                                @csrf
                                <input hidden type="text" name="id" value="{{ $file->id }}">
                                <div class="attach-item d-flex gap-4 justify-content-between align-items-center">
                                    <h6 class="file-info">
                                        {{ translate('File-') . $loop->index + 1 }}
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="icon-btn icon-btn-md info circle download-btn"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title=" {{ translate('Download')}}">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{ asset('assets/global/js/summernote.min.js') }}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{ asset('assets/global/js/editor.init.js') }}"></script>
@endpush
