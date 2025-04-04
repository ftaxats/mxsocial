
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModal"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content notification-modal">
      <div class="modal-body">
        <div class="modal-delete-noti">
          <div class="notification-modal-icon">
              <img src="{{asset('assets/images/default/trash-bin.gif')}}" class="action-img" alt="trash-bin.gif">
          </div>
          <div class="notification-modal-content">
              <h5>   {{trans('default.are_you_sure')}}</h5>
              <p class="warning-message"></p>
          </div>
        </div>
        <div class="modal-footer d-flex">
          <button type="button"
              class="i-btn btn--lg btn--outline radius-8 flex-grow-1"
              data-bs-dismiss="modal">
               {{translate("No")}}
          </button>
            <div class="actionbtn flex-grow-1">
                <a href="javascript:void(0)" class="i-btn btn--lg btn--danger w-100 radius-8"
                id="action-href">
                    {{translate('Yes')}}
               </a>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>



