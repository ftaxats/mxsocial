<div class="modal fade" id="planUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="planUpdate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >
                    {{translate('Subscription Update')}}
                </h5>
                <button class="close-btn" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>


            <form action="{{route('admin.user.subscription')}}"  method="post" enctype="multipart/form-data">
                    @csrf
                      <input type="hidden" name="id" value="{{ $user->id }}">
                      <div class="modal-body">
                            <div class="form-inner mb-2">
                                <label for="Subscription"  class="mb-2">
                                    {{ translate('Running Subscription') }} <span class="text-danger">*</span>
                                </label>
                                <select required class="select2" name="package_id" id="Subscription" >
                                    <option value="">
                                        {{translate("Select Package")}}
                                    </option>
                                    @foreach ($packages as $package)
                                        <option {{$user->runningSubscription?->package_id == $package->id ? "selected" :"" }} value="{{$package->id}}">
                                            {{$package->title}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-inner">
                                <label for="subscriptionRemarks">
                                    {{ translate('Remarks') }}
                                    <small class="text-danger">*</small>
                                </label>
                                <textarea required placeholder="{{ translate('Type Here ...') }}" name="remarks" id="subscriptionRemarks" cols="30"
                                    rows="10"></textarea>
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