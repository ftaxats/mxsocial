<!doctype html>
<html lang="{{App::getLocale()}}" dir="ltr" data-sidebar="open" color-scheme="light">
<head>
    <meta charset="utf-8" />
    <title>{{@site_settings("site_name")}} {{site_settings('title_separator')}} {{@translate($title)}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link nonce="{{ csp_nonce() }}" rel="shortcut icon" href='{{imageURL(@site_logo("favicon")->file,"favicon",true)}}' alt="{{@site_logo('site_favicon')->file?->name}}">
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/bootstrap-icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/backend/css/main.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/toastr.css')}}" rel="stylesheet" type="text/css" />
    @include('partials.theme')
</head>
<body>
    <div class="form-section pt-100 pb-100">
        <div class="container">
            @yield('main_content')
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/jquery-3.7.1.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}"src="{{asset('assets/global/js/toastify-js.js')}}"></script>
    <script  nonce="{{ csp_nonce() }}"src="{{asset('assets/global/js/helper.js')}}"></script>
    @include('partials.notify')
    @stack('script-push')
</body>
</html>


