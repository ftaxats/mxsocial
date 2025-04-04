
@if ($errors->any())
    @foreach($errors->all() as $message)
        <script nonce="{{ csp_nonce() }}">
            "use strict";      
            toastr("{{translate($message)}}",'danger')
        </script>
    @endforeach
@endif

@if (Session::has('success') )
    <script nonce="{{ csp_nonce() }}">
        "use strict";
        toastr("{{translate(Session::get('success'))}}",'success')
    </script>
@endif

@if (Session::has('error'))
    <script nonce="{{ csp_nonce() }}">
        "use strict";
        toastr("{{translate(Session::get('error'))}}",'danger')
    </script>
    @php
      session()->forget('error');
    @endphp
@endif
