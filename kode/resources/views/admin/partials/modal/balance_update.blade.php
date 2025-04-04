<div class="modal fade" id="balanceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="balanceModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >
                    {{translate('Deposit Balance')}}
                </h5>
                <button class="close-btn" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{route('admin.user.balance')}}" method="post" enctype="multipart/form-data">
                <input value="{{$user->id}}" hidden name="id"  type="text">
                <input  hidden name="type" id="type"  type="text">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="amount">
                                {{translate('Amount')}}
                                    <small class="text-danger">*</small>
                            </label>
                            <input placeholder="{{translate('Enter amount')}}" min='1' required type="number" name="amount" id="amount" value="{{old('amount')}}">
                        </div>
                        <div class="col-lg-6 deposit-method d-none">
                            <label for="payment_id">
                                {{translate('Deposit Method')}}
                                    <small class="text-danger">*</small>
                            </label>
                            <select class="select-method" name="payment_id" id="payment_id">
                                <option value="">
                                    {{translate('Select method')}}
                                </option>
                                @foreach ($methods as $method )
                                    <option {{old("payment_id") == $method->id ? "selected" : "" }} value="{{$method->id}}">
                                         {{$method->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 withdraw-method d-none">
                            <label for="method_id">
                                {{translate('Withdraw Method')}}
                                    <small class="text-danger">*</small>
                            </label>
                            <select class="select-method" name="method_id" id="method_id">
                                <option value="">
                                    {{translate('Select method')}}
                                </option>
                                @foreach ($withdraw_methods as $withdrawMethod )
                                    <option {{old("method_id") == $withdrawMethod->id ? "selected" : "" }} value="{{$withdrawMethod->id}}">
                                         {{$withdrawMethod->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <label for="remarks">
                                {{translate('Remarks')}}
                                    <small class="text-danger">*</small>
                            </label>
                            <textarea placeholder="{{translate('Remarks')}}" name="remarks" id="remarks" cols="30" rows="10">{{old("remarks")}}</textarea>
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