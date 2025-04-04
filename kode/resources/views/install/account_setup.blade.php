@extends('install.layouts.master')
@section('content')
    <div class="installer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    @include('install.partials.progress_bar')
                    <div class="installer-wrapper bg--light account-setup-step">
                        <div class="i-card-md">
   
                            <form action="{{route('install.account.setup.store')}}" method="post">
                                @csrf
                                <div class="p-4 bg--danger-light mb-4">
                                    <p class="text--dark"><span class="bg--danger text-white py-0 px-2 d-inline-block me-2">Warning  :</span>  
                                            @php echo (trans('default.account_setup_warning'))  @endphp
                                    </p>
                                </div>
                                <div class="row g-md-4 g-3  justify-content-center">

                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="username">
                                                Username
                                            </label>
                                            <input name="username" value="{{old('username')}}" type="text" id="username" placeholder="Enter your username">
                                        </div>
                                    </div>
        
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="email">
                                                Email
                                            </label>
                                            <input name="email"   value="{{old('email')}}" type="email" id="email" placeholder="Enter your email">
                                        </div>
                                    </div>
        
                                    <div class="col-lg-12">
                                        <div class="form-inner">
                                            <label for="password">
                                                Password <span class="text-danger">(Min :5)</span>
                                            </label>
                                            <input name="password" value="{{old('password')}}"  type="text" id="password" placeholder="Enter your password">
                                        </div>
                                    </div>


                                    <div class="col-md-6 text-center  ">
                                        <div class="d-flex gap-2  justify-content-center">
                                            <button name="force" value="0" type="submit"  class="i-btn ai--btn btn--lg  btn--success btn--primary"> 
                                                {{trans('default.btn_import')}}
                                                <i class="ms-2 bi bi-database"></i>
                                            </button>
                                            <button name="force"  value="1" type="submit" class="i-btn  ai--btn btn--lg danger btn--primary"> 
                                                {{trans('default.btn_force_import')}}  <i class="ms-2 bi bi-database-down"></i>
                                            </button>
                                        </div>
                                    </div>


                                </div> 
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection