@extends('layouts.master')
@section('content')
@php
   $balance         = auth_user("web")->balance;
   $currencySymbol  = session('currency')?session('currency')->symbol : base_currency()->symbol;
@endphp

<div class="row">
    <div class="col-xl-9 col-lg-10 mx-auto">
      <div class="i-card-md">
        <div class="card-body">
          <div class="manual-pay-card">
            <div class="manual-pay-top text-lg-center mb-4">
              <h3>{{translate('Please follow the instruction below')}}</h3>
            </div>
            <form action="{{route('user.withdraw.request.submit')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="amount" value="{{($amount)}}">
                  <div class="row g-4">
                    <div class="col-12">
                      <div class="d-flex flex-column align-items-center justify-content-start gap-2">
                          <div class="avatar-xl profile-picture">
                              <img src='{{imageURL(@$method->file,"withdraw_method",true)}}' alt="{{translate('Withdraw method image preview')}}" class="rounded-50">
                          </div>
                          <div class="text-start">
                              <h5 class="fs-20">
                                  {{@$method->name}}
                              </h5>
                          </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="p-3  bg-danger-soft">
                          <p><span class="i-badge-solid danger me-2">{{translate("note")}}  :</span>
                              {{@$method->note}}
                          </p>
                      </div>
                   </div>
                    <div class="col-12">
                        <ul class="payment-details list-group">
                            <li class="list-group-item" aria-current="true">
                                <h5>
                                    {{translate("Withdraw Details")}}
                                </h5>
                            </li>

                            <li class="list-group-item">
                              <p>{{translate('Limit')}}</p>
                              <h6>
                                  {{num_format(number:$method->minimum_amount)}} -  {{num_format(number:$method->maximum_amount)}}
                              </h6>
                            </li>

                            <li class="list-group-item">
                                <p>{{translate('Requested Amount')}}</p>
                                <h6>{{num_format($amount)}}</h6>
                            </li>

                            <li class="list-group-item">
                              <p>{{translate("Charge")}}</p>
                              <h6>
                                @php
                                   $charge  = ((float)$method->fixed_charge + ($amount  * (float)$method->percent_charge / 100));
                                @endphp
                                {{num_format(number:$charge)}}
                              </h6>
                            </li>

                            <li class="list-group-item">
                              <p>{{translate("Payable")}}</p>
                              <h6>{{num_format(number:$charge+$amount)}}</h6>
                            </li>

                            <li class="list-group-item">
                                <p>{{translate("Duration")}}</p>
                                <h6>
                                    @php
                                        use Carbon\Carbon;
                                        $durationInHours = (int)$method->duration;
                                        $startTime = Carbon::now();
                                        $endTime   =    $startTime->addHours($durationInHours)
                                    @endphp
                                     {{diff_for_humans($endTime)}}
                                </h6>
                            </li>
                        </ul>
                    </div>


                    @if(optional($method)->parameters)
                        @foreach(optional($method)->parameters as $k => $v)
                             @if($v->type == "text" || $v->type == "password"  )
                                <div class="col-md-12">
                                    <div class="form-inner mb-0">
                                        <label for="{{$k}}">{{translate($v->field_label)}} @if($v->validation == 'required') <small class="text-danger">*</small>  @endif </label>
                                        <input value="{{old($k)}}" id="{{$k}}" placeholder="{{k2t($k)}}" type="{{$v->type}}" name="{{$k}}"   @if($v->validation == "required") required @endif>
                                    </div>
                                </div>
                             @elseif($v->type == "textarea")
                                <div class="col-12">
                                    <div class="form-inner mb-0">
                                        <label for="{{$k}}">{{translate($v->field_label)}} @if($v->validation == 'required') <small class="text-danger">*</small>  @endif </label>
                                        <textarea id="{{$k}}" placeholder="{{k2t($k)}}" name="{{$k}}"  rows="3" @if($v->validation == "required") required @endif>{{old($k)}}</textarea>
                                    </div>
                                </div>

                            @elseif($v->type == "file")
                                <div class="col-md-12">
                                    <div class="form-inner mb-0">
                                        <label for="{{$k}}">{{translate($v->field_label)}} @if($v->validation == 'required') <small class="text-danger">*</small>  @endif </label>
                                        <input id="{{$k}}" type="file" name="{{$k}}" accept="image/*"
                                            @if($v->validation == "required") required @endif>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    <div class="col-12">
                      <button type="submit"
                        class="i-btn btn--primary btn--lg capsuled">
                            {{translate("Submit")}}
                      </button>
                    </div>
                  </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
