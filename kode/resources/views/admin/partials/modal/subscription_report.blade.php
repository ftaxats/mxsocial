<div class="modal fade" id="report-info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="report-info"   aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{translate('Subscription Information')}}
                </h5>
                <button class="close-btn" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label for="content" class="form-label" >
                                {{translate('Remark')}}
                            </label>
                            <textarea disabled name="content" id="content" cols="30" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <ul class="list-group list-group-flush" id="additionalInfo">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="i-btn btn--md ripple-dark danger" data-anim="ripple" data-bs-dismiss="modal">
                    {{translate("Close")}}
                </button>
            </div>
        </div>
    </div>
</div>