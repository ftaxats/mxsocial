<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content notification-modal">
      <div class="modal-body">
        <div class="modal-delete-noti">
          <div class="notification-modal-icon bulk-action-img">
            <img src="{{asset('assets/images/default/trash-bin.gif')}}" class="bulk-warning-image" alt="trash-bin.gif">
          </div>
          <div class="notification-modal-content">
            <h5>{{trans('default.are_you_sure')}}</h5>
            <p class="warning-message bulk-warning">
                {{ @$message ?? translate('Do You Want To Delete These Records??')}}
            </p>
          </div>
        </div>
          <div class="modal-footer">
            <button type="button"
              class="i-btn btn--lg bg-soft-warning"
              data-bs-dismiss="modal">
                 {{translate("No")}}
            </button>
            <button data-type ="delete" name="bulk_status" value="delete"  class="i-btn btn--lg delete-btn btn-delete bulk-action-btn bulk-btn">
                {{translate("Delete")}}
            </button>
          </div>
      </div>
    </div>
  </div>
</div>

