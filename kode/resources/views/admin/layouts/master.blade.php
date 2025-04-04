
<!DOCTYPE html>
<html lang="{{App::getLocale()}}" data-sidebar="open">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{csrf_token()}}" />
    <title>{{@site_settings("site_name")}} {{site_settings('title_separator')}} {{@translate($title)}}</title>

    <link  nonce="{{ csp_nonce() }}" rel="shortcut icon" href="{{imageURL(@site_logo('favicon')->file,'favicon',true)}}">
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/bootstrap-icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/line-awesome.min.css')}}" rel="stylesheet"  type="text/css"/>
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/nice-select.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link  nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/simplebar.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/frontend/css/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/dataTables.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/backend/css/post.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/backend/css/main.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/toastr.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/backend/css/custom.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/custom.css')}}" rel="stylesheet" type="text/css" />

    @include('partials.theme')
    @stack('style-include')
    @stack('styles')

    @cspMetaTag(App\Policies\CustomCspPolicy::class)

  </head>
  <body>
    @include('admin.partials.topbar')
        <div class="dashboard-wrapper">
            @include('admin.partials.sidebar')
            <div class="main-content">
                @if(!request()->routeIs('admin.home') && !request()->routeIs('admin.social.post.analytics') && !request()->routeIs('admin.user.statistics') )
                    @include('admin.partials.breadcrumb')
                @endif
                @yield('content')
            </div>
        </div>
    @yield("modal")


    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/jquery-3.7.1.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/simplebar.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/dataTables.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/backend/js/app.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/frontend/js/swiper-bundle.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/main.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/nice-select.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/select2.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/toastify-js.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/helper.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/purify.js')}}"></script>

    @include('partials.notify')
    @stack('script-include')
    @stack('script-push')

    <script nonce="{{ csp_nonce() }}">

    (function($){
        "use strict";

        $('img[data-fallback]').on('error', function() {
            var fallbackImage = $(this).data('fallback');
            $(this).attr('src', fallbackImage);
        });


        var inputTags = document.querySelectorAll('input[type="checkbox"]');

        inputTags.forEach(function(inputTag){

            if(inputTag.hasAttribute('disabled')){
                inputTag.style.backgroundColor = 'rgba(0,0,0,.8)';
            }
        })

        window.onload = function () {
           $('.table-loader').addClass("d-none");

        }


        $(document).on('click', '.copy-trx ', function (e) {
            e.preventDefault()
            var data = $(this).parent().find('.trx-number').html()
            var $tempInput = $('<input>');
            $('body').append($tempInput);
            $tempInput.val(data.trim()).select();
            document.execCommand('copy');
            $tempInput.remove();
            toastr('Copied Successfully', 'success')
        })

        // update status event start
        $(document).on('click', '.status-update', function (e) {

            const id = $(this).attr('data-id')
            const key = $(this).attr('data-key')
            var column = ($(this).attr('data-column'))
            var route = ($(this).attr('data-route'))
            var modelName = ($(this).attr('data-model'))
            var status = ($(this).attr('data-status'))
            const data = {
                'id': id,
                'model': modelName,
                'column': column,
                'status': status,
                'key': key,
                "_token" :"{{csrf_token()}}",
            }
            updateStatus(route, data)
        })

        // update status method
        function updateStatus(route, data) {
            var responseStatus;
            $.ajax({
                method: 'POST',
                url: route,
                data: data,
                dataType: 'json',
                success: function (response) {

                    if (response) {
                        responseStatus = response.status? "success" :"danger"
                        toastr(response.message,responseStatus)
                        if(response.reload){
                            location.reload()
                        }
                    }
                },
                error: function (error) {

                    handleAjaxError(error);
                   
                }
            })
        }

        // read notification
        $(document).on('click','.read-notification',function(e){

            e.preventDefault()
            var href = $(this).attr('data-href')
            var id = $(this).attr('data-id')
            readNotification(href,id)

        })

        // read Notification
        function readNotification(href,id){

            $.ajax({
                method:'post',
                url: "{{route('admin.read.notification')}}",
                data:{
                    "_token": "{{ csrf_token()}}",
                    'id':id
                },
                dataType: 'json'
                }).then(response =>{
                if(!response.status){
                    toastr(response.message,'danger')
                }
                else{
                    window.location.href = href
                }}).fail((jqXHR, textStatus, errorThrown) => {
                    toastr(jqXHR.statusText, 'danger');
                });
        }

        /** delete ,restore , bulk action */

        $(document).on('click','.bulk-action-btn' ,function(e){
            e.preventDefault()
            var type = $(this).attr("data-type")
            var value = $(this).val()

            const checkedIds = $('.data-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            $('#bulkid').val(JSON.stringify(checkedIds));
            $('#value').val(value);
            $('#type').val(type);

            $("#bulkActionForm").submit()

        });

        $(document).on('click','.bulk-action-modal',function(e){
            e.preventDefault()
            var type = $(this).attr("data-type");
            var src = "{{asset('assets/images/default/trash-bin.gif')}}";
            $('.bulk-btn').html('{{translate("Delete")}}')
            if(type){
                if(type != "delete"){
                    $('.bulk-btn').attr("data-type",type)
                    $('.bulk-btn').val(type)
                    $('.bulk-warning').html($(this).attr("data-message"))
                    if(type == 'restore'){
                        $('.bulk-btn').html('{{translate("Restore")}}')
                         src = "{{asset('assets/images/default/restore.gif')}}";
                    }
                }
            }

            $(".bulk-warning-image").attr("src",src)
            var modal = $('#bulkActionModal')
            modal.modal('show')
        })

        //delete event start
        $(document).on('click', ".delete-item", function (e) {
            e.preventDefault();
            var href = $(this).attr('data-href');
            var message = 'Are you sure you want to remove these record ?'
            if (($(this).attr('data-message'))) {
                message = $(this).attr('data-message')
            }
            var cleanContent = DOMPurify.sanitize(message);
            var src = "{{asset('assets/images/default/trash-bin.gif')}}";
            $('.action-img').attr("src",src)
            $("#action-href").attr("href", href);
            $(".warning-message").html(cleanContent)
            $("#actionModal").modal("show");
        })

        //restore event start
        $(document).on('click', ".restore-item", function (e) {

            e.preventDefault();
            var href = $(this).attr('data-href');

            var src = "{{asset('assets/images/default/restore.gif')}}";
            var message = 'Are you sure! you want to restore these record ?'
            if (($(this).attr('data-message'))) {
                message = $(this).attr('data-message')
            }

            var cleanContent = DOMPurify.sanitize(message);

            $("#action-href").attr("href", href);
            $('.action-img').attr("src",src)
            $(".warning-message").html(cleanContent)
            $("#actionModal").modal("show");
        })

        // update seettings
        $(document).on('submit','.settingsForm',function(e){

                var data  =   new FormData(this)
                var route =  $(this).attr('data-route')
                                    ? $(this).attr('data-route')
                                    :  "{{route('admin.setting.store')}}"

                var submitButton = $(e.originalEvent.submitter);

                $.ajax({
                method:'post',
                url: route,
                beforeSend: function() {
                        submitButton.find(".note-btn-spinner").remove();

                        submitButton.append(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
                                <span class="visually-hidden"></span>
                            </div>`);

                },
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                data: data,
                success: function(response){
                    var className = 'success';
                    if(!response.status){
                        className = 'danger';
                    }
                    toastr( response.message,className)
                },
                error: function (error){

                    handleAjaxError(error);
                        
                },
                complete: function() {
                    submitButton.find(".note-btn-spinner").remove();
                },

            })

            e.preventDefault();
        });

        if (!$(".Paginations").find("nav").length > 0) {
            $(".Paginations").addClass('d-none')
        }

        // Summer note
        $(document).on("click", ".close", function (e) {
            $(this).closest(".modal").modal("hide");
        });
    })(jQuery);
    </script>
  </body>
</html>
