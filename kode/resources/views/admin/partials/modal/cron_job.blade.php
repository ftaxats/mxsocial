<div class="modal fade" id="cronjob" tabindex="-1" aria-labelledby="cronjob" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-icon" >
                    {{translate('Cron Job Setup')}}
                </h5>
                <button class="close-btn" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="queue_url" >{{translate('Queue')}} <span class="text-danger">* {{translate('Set time for 1 minute')}}</span></label>
                    <div class="input-group">
                        <input id="queue_url" readonly class="form-control" value="curl -s {{route('queue.work')}}">
                        <button data-type="modal"  data-text ="curl -s {{route('queue.work')}}" class="copy-text btn btn-info" type="button"><i class="las la-copy"></i></button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="cron_url">{{translate('Cron Job')}} <span class="text-danger">* {{translate('Set time for 1 minute')}}</span></label>
                    <div class="input-group">
                        <input id="cron_url" readonly class="form-control" value="curl -s {{route('cron.run')}}">
                        <button data-type="modal" data-text ="curl -s {{route('cron.run')}}" class="copy-text btn btn-info" type="button"><i class="las la-copy"></i></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="i-btn btn--md ripple-dark" data-anim="ripple" data-bs-dismiss="modal">
                    {{translate("Close")}}
                </button>
            </div>
        </div>
    </div>
</div>