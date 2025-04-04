@extends('admin.layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/viewbox/viewbox.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
    <div class="row justify-content-center g-4 mb-4">
        @php
            $kycData = true;

            if(is_array($report->kyc_data) && count($report->kyc_data) < 1){
                $kycData = false;
            }

        @endphp

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
                                                    "title"   =>  translate('Date'),
                                                    "value"   =>  diff_for_humans($report->created_at)
                                    ],
                                    [
                                                    "title"     =>  translate('Status'),
                                                    "is_html"   =>  true,
                                                    "value"     =>  kyc_status($report->status)
                                    ],
                                    [
                                                    "title"     =>  translate('Note'),
                                                    "value"     =>  $report->notes ?? ('--')
                                    ],
                                        
                        ];

                    @endphp
                    @include('admin.partials.custom_list',['list'  => $lists])

                </div>
            </div>
        </div>
        @if ($kycData)
            <div class="col-xl-7 col-lg-6 col-md-6">
                <div class="i-card-md">
                    <div class="card--header">
                        <h4 class="card-title">
                            {{ translate('Custom  Information') }}
                        </h4>
                    </div>
                    <div class="card-body">

                        <ul class="custom-info-list list-group-flush">

                            @foreach ($report->kyc_data as $k => $v)
                                <li>
                                    <span>{{  translate(k2t($k)) }}:</span> 
                                    <span>
                                        {{ $v }}
                                    </span>
                                 
                                </li>
                            @endforeach

                            @foreach ($report->file as $file)
                                <li>
                                    <span>{{ translate(k2t($file->type)) }} :</span> 
                                    <div class="custom-profile">
                                        <div class="image-v-preview">
                                                <img class="image-v-preview" src='{{imageURL($file,"kyc",true)}}'
                                                alt="{{ @$file->name }}">
                                        </div>
                                    </div>
                                </li>
                            @endforeach

                        </ul>


                        @if(App\Enums\KYCStatus::value("REQUESTED",true) == $report->status)
                        
                            <div class="d-flex justify-content-center p-4 gap-2">
                                <div class="action">
                                    <a href="javascript:void(0)" data-status = '{{App\Enums\KYCStatus::value("APPROVED")}}';    class="i-btn btn--sm success update ">
                                        <i class="las la-check-double me-1"></i>  {{translate('Approve')}}
                                    </a>
                                </div>
                                <div class="action">
                                    <a href="javascript:void(0)"   data-status = '{{App\Enums\KYCStatus::value("REJECTED")}}'  class="i-btn btn--sm danger update">
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
    @include('admin.partials.modal.kyc_update')
@endsection

@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/viewbox/jquery.viewbox.min.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";


        $(document).on('click','.update',function(e){
            var status =($(this).attr('data-status'))
            var modal  = $('#updateKyc')
            modal.find('input[name="status"]').val(status)
            modal.modal('show')
        })

	})(jQuery);
</script>
@endpush
