<div class="modal fade" id="updateWtihdraw" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateWtihdraw" aria-hidden="true">
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
            <form action="{{route('admin.withdraw.report.update')}}" id="updateModalForm" method="post" enctype="multipart/form-data">
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