
<div class="tab-pane" id="productnav-draft" role="tabpanel">
    <div class="py-4 d-flex justify-content-center align-items-center text-center">
        <div class="trash-bin nodata-wrapper">
            <img src="{{asset('assets/images/default/search.gif')}}" alt="search.gif">
            <h5 class="mt-4">
                {{

                    @$custom_message 
                            ? translate($custom_message) 
                            : trans('default.no_result_found')
                }}
            </h5>
         </div>
    </div>
</div>
