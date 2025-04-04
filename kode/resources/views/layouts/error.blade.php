<!DOCTYPE html>
<html lang="{{App::getLocale()}}" class="sr" data-sidebar="open">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{translate('Error')}}{{@$title?'-'.@$title:""}}</title>

    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/bootstrap-icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/frontend/css/root.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/frontend/css/style.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/frontend/css/custom.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/frontend/css/common.css')}}" rel="stylesheet" type="text/css" />
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/toastr.css')}}" rel="stylesheet" type="text/css" />

    @cspMetaTag(App\Policies\CustomCspPolicy::class)

    <style nonce="{{ csp_nonce() }}">
      .invalid-license-title {
          font-size:60px !important;
      }
      .access-denied{
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

      }
      .error-number {
            font-size: 12rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 2rem;
            background: linear-gradient(to right, #ffffff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        h2 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeIn 0.8s forwards;
            animation-delay: 0.5s;
        }

        p {
            font-size: 1.25rem;
            max-width: 36rem;
            margin: 0 auto 1rem;
            opacity: 0;
            animation: fadeIn 0.8s forwards;
            animation-delay: 0.8s;
        }

        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .error-number {
                font-size: 8rem;
            }

            h2 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }

            .button {
                font-size: 1rem;
                padding: 0.875rem 2rem;
            }
        }
    </style>



  </head>

  <body>
    <main class="main">
        <section class="overflow-x-hidden d-flex justify-content-center align-items-center">
            <div class="error-wrapper py-5 mt-5">
                <div class="container-fluid">
                    <div class="row gx-4 gy-5 justify-content-center align-items-center">
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/jquery-3.7.1.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/toastify-js.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/helper.js')}}"></script>
    @include('partials.notify')
  </body>
