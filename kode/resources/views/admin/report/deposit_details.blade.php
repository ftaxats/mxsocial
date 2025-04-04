@extends('admin.layouts.master')

@push('style-include')
    <link  nonce="{{ csp_nonce() }}"   media="screen"   href="{{asset('assets/global/css/viewbox/viewbox.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
           <div class="row g-4 mb-4 justify-content-center">
          

                <div class="col-xl-7">
                    <div class="i-card-md">
                        <div class="card--header">
                            <h4 class="card-title">
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
                                                            "title"  =>  translate('Payment Method'),
                                                            "value"  =>  $report->method->name
                                            ],
                                            [
                                                            "title"  =>  translate('Amount'),
                                                            "value"  =>  num_format($report->amount,@$report->currency)
                                            ],
                                            [
                                                            "title"  =>  translate('Charge'),
                                                            "value"  =>  num_format($report->charge,@$report->currency)
                                            ],
                                            [
                                                            "title"   =>  translate('Rate'),
                                                            "value"   => num_format(1,$report->currency) ." = ". num_format($report->rate,@$report->method->currency)
                                            ],
                                            [
                                                            "title"   =>  translate('Final Amount'),
                                                            "value"   =>  num_format($report->final_amount,@$report->method->currency)
                                            ],
                                            [
                                                            "title"   =>  translate('Date'),
                                                            "value"   =>  diff_for_humans($report->created_at)
                                            ],
                                            [
                                                            "title"     =>  translate('Status'),
                                                            "is_html"   =>  true,
                                                            "value"     =>   payment_status($report->status)
                                            ],
                                            [
                                                            "title"     =>  translate('Feedback'),
                                                            "value"     =>  $report->feedback ?? "-"
                                            ],
                                                
                                    ];

                            @endphp
                            @include('admin.partials.custom_list',['list'  => $lists])
                        </div>
                    </div>
                </div>
                
                @if(! (is_array($report->custom_data) && count($report->custom_data) < 1))
                    <div class="col-xl-7 col-lg-6 col-md-6">
                        <div class="i-card-md">
                            <div class="card--header">
                                <h4 class="card-title">
                                    {{ translate('Custom  Information') }}
                                </h4>
                            </div>
                            <div class="card-body">

                                @include('admin.partials.custom_list',['db_list'  => true , 'lists' => $report->custom_data,'file_path'=> "payment"])
                            
                                @if(App\Enums\DepositStatus::value("PENDING",true) == $report->status)
                                    <div class="d-flex justify-content-center p-4 gap-2">
                                        <div class="action">
                                            <a href="javascript:void(0)" data-status = '{{App\Enums\DepositStatus::value("PAID")}}';    class="i-btn btn--sm success update ">
                                                <i class="las la-check-double me-1"></i>  {{translate('Approve')}}
                                            </a>
                                        </div>
                                        <div class="action">
                                            <a href="javascript:void(0)"   data-status = '{{App\Enums\DepositStatus::value("REJECTED")}}'  class="i-btn btn--sm danger update">
                                                <i class="las la-times-circle me-1"></i> {{translate('Reject')}}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
    </div>
@endsection

@section('modal')

<div class="modal fade" id="updateDeposit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateDeposit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >
                    {{translate('Update')}}
                </h5>
                <button class="close-btn" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{route('admin.deposit.report.update')}}" id="updateModalForm" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="{{$report->id}}" class="form-control" >
                    <input type="hidden" name="status" id="status"  class="form-control" >
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-inner">
                                <label for="feedback">
                                    {{translate('Feedback')}}
                                        <small class="text-danger">*</small>
                                </label>
                                <textarea required placeholder='{{translate("Type Here ...")}}' name="feedback" id="feedback" cols="30" rows="5"></textarea>
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
    <script  type="text/javascript"  nonce="{{ csp_nonce() }}"  src="{{asset('assets/global/js/viewbox/jquery.viewbox.min.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";

        $(document).on('click','.update',function(e){

            e.preventDefault()

            var status =($(this).attr('data-status'))
            var modal  = $('#updateDeposit')
            
            modal.find('input[name="status"]').val(status)
            modal.modal('show')
        })

	})(jQuery);
</script>
@endpush
