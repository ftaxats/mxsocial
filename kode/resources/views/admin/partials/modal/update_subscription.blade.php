<div class="modal fade" id="updatesubscription" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatesubscription" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >
                        {{translate('Update Subscription')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.subscription.report.update')}}" id="updateModalForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" class="form-control" >
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="Status">
                                        {{translate('Status')}}   <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="status"  id="Status">
                                        @foreach ( App\Enums\SubscriptionStatus::toArray() as $k => $v )
                                           <option value="{{$v}}">{{$k}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="expired_at">
                                        {{translate('Expire Date')}} <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" required name="expired_at" id="expired_at">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="remark">
                                        {{translate('Remark')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                       <textarea required placeholder='{{translate("Type Here ...")}}' name="remarks" id="remark" cols="30" rows="5"></textarea>
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