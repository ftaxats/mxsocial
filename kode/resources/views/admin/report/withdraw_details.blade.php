@extends('admin.layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/viewbox/viewbox.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
    <div class="row justify-content-center g-4 mb-4">

        <div class="col-xl-7">
            <div class="i-card-md">
                <div class="card-body">

                        <div class="card--header">
                            <h4 class="card-title mb-3">
                                {{ translate('Basic Information') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            @php
                                $lists  =  [
                                                    
                                                [
                                                                "title"  =>  translate('User'),
                                                                "href"   =>  route("admin.user.show",$report->user?->uid),
                                                                "value"  =>  $report->user?->name,
                                                ],
                                                [
                                                                "title"  =>  translate('Transaction ID'),
                                                                "value"  =>  $report->trx_code
                                                ],
                                                [
                                                                "title"  =>  translate('Receivable Amount'),
                                                                "value"  =>  num_format($report->amount,@$report->currency)
                                                ],
                                                [
                                                                "title"  =>  translate('Charge'),
                                                                "value"  =>  num_format($report->charge,@$report->currency)
                                                ],
                                                [
                                                                "title"  =>  translate('Final Amount'),
                                                                "value"  =>  num_format($report->final_amount,@$report->currency)
                                                ],
                                                [
                                                                "title"   =>  translate('Date'),
                                                                "value"   =>  diff_for_humans($report->created_at)
                                                ],
                                                [
                                                                "title"     =>  translate('Status'),
                                                                "is_html"   =>  true,
                                                                "value"     =>  withdraw_status($report->status)
                                                ],
                                                [
                                                                "title"     =>  translate('Feedback'),
                                                                "value"     =>  $report->notes ?? ('--')
                                                ],
                                                    
                                        ];

                            @endphp
                            @include('admin.partials.custom_list',['list'  => $lists])
                        </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="i-card-md">
                <div class="card-body">
                    <div class="card--header">
                        <h4 class="card-title mb-3">
                            {{ translate('Custom  Information') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        
                        @if($report->custom_data)
                        
                            @include('admin.partials.custom_list',['db_list'  => true , 'lists' => $report->custom_data,'file_path'=> "withdraw"])

                            @if(App\Enums\WithdrawStatus::value("PENDING",true) == $report->status)

                                <div class="d-flex justify-content-center p-4 gap-2">
                                    <div class="action">
                                        <a href="javascript:void(0)" data-status = '{{App\Enums\WithdrawStatus::value("APPROVED")}}';    class="i-btn btn--sm success update ">
                                            <i class="las la-check-double me-1"></i>  {{translate('Approve')}}
                                        </a>
                                    </div>
                                    <div class="action">
                                        <a href="javascript:void(0)"   data-status = '{{App\Enums\WithdrawStatus::value("REJECTED")}}'  class="i-btn btn--sm danger update">
                                            <i class="las la-times-circle me-1"></i> {{translate('Reject')}}
                                        </a>
                                    </div>
                                </div>

                            @endif
                        @else
                           @include('admin.partials.not_found',['custom_message' => "No Data found!!"])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('modal')
   @include('admin.partials.modal.withdraw_update')
@endsection

@push('script-include')
    <script src="{{asset('assets/global/js/viewbox/jquery.viewbox.min.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";


        $(document).on('click','.update',function(e){
            e.preventDefault()
            var status =($(this).attr('data-status'))
            var modal  = $('#updateWtihdraw')
            modal.find('input[name="status"]').val(status)
            modal.modal('show')
        })

	})(jQuery);
    
</script>

@endpush
