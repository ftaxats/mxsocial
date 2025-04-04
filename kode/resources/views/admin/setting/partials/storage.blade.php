<div class="i-card-md">
    <div class="card--header">
        <h4 class="card-title">
            {{  Arr::get($tab,'title') }}
        </h4>
    </div>
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs style-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#local" role="tab" aria-selected="true">
                    {{translate('local')}}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#aws-s3" role="tab" aria-selected="false" tabindex="-1">
                    {{translate ('AWS S3')}}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#ftp" role="tab" aria-selected="false" tabindex="-1">
                    {{translate ('FTP')}}
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content text-muted">
            <div class="tab-pane active show" id="local" role="tabpanel">
                <form  class="settingsForm"  data-route="{{route('admin.setting.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex align-items-center gap-3 mb-20">
                        <label >{{translate('Local Storage')}}</label>

                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input {{ site_settings('storage') == "local" ? 'checked' :"" }} type="checkbox" class="form-check-input"
                            value ='local'
                            name="site_settings[storage]"  id="storage">
                            <label class="form-check-label mb-0" for="storage"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-inner">
                                <label for="mime_types">
                                    {{translate('Supported File Types')}}  <small class="text-danger" >*</small>
                                </label>
                                <select multiple class="select2-multi" name="site_settings[mime_types][]" id="mime_types">
                                    @foreach(config('settings')['file_types'] as $file_type)
                                        <option {{in_array($file_type,$mimeTypes) ? "selected" :"" }} value="{{$file_type}}">
                                            {{$file_type}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-inner">
                                <label for="max_file_upload">
                                    {{translate('Maximum File Upload')}}  <small class="text-danger" >*
                                    </small>
                                </label>

                                <input type="number" min="1" max="10" required  value ="{{site_settings('max_file_upload')}}" name="site_settings[max_file_upload]" id="max_file_upload">
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-inner">
                                <label for="max_file_size" >
                                    {{translate('Max File Upload size')}}  <small class="text-danger" >*
                                        ({{translate('In KB')}})
                                    </small>
                                </label>
                                <input type="number" min="1" id="max_file_size"  required  value ="{{site_settings('max_file_size')}}" name="site_settings[max_file_size]">
                            </div>
                        </div>
                        <div class="col-12 ">
                            <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                                {{translate("Submit")}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- aws file system -->
            <div class="tab-pane" id="aws-s3" role="tabpanel">
                <form  class="settingsForm"  data-route="{{route('admin.setting.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex align-items-center gap-3 mb-20">
                        <label>{{translate('S3 Storage')}}</label>
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                                <input {{ site_settings('storage') == "s3" ? 'checked' :"" }} type="checkbox" class="form-check-input"
                                value ='s3'
                                name="site_settings[storage]"  id="AWSstorage">
                                <label class="form-check-label mb-0" for="AWSstorage"></label>
                        </div>
                    </div>
                    <div class="row">
                        @foreach($awsSettings as $awsKey => $val)
                            <div class="col-xl-6">
                                <div class="form-inner">
                                    <label for="aws_s3-{{$awsKey}}">
                                        {{
                                            ucfirst(str_replace('_',' ',$awsKey))
                                        }}  <small class="text-danger" >*</small>
                                    </label>
                                    <input required type="text" name="site_settings[aws_s3][{{$awsKey}}]" id="aws_s3-{{$awsKey}}"  value="{{is_demo() ? '@@@' :$val}}" placeholder="**********">
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <button type="submit" class="i-btn btn--md ai-btn btn--primary" data-anim="ripple">
                                {{translate("Submit")}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- ftp file system -->
            <div class="tab-pane" id="ftp" role="tabpanel">
                <form  class="settingsForm"  data-route="{{route('admin.setting.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex align-items-center gap-3 mb-20">
                        <label> {{translate('FTP Storage')}}</label>
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input {{ site_settings('storage') == "ftp" ? 'checked' :"" }} type="checkbox" class="form-check-input"
                            value ='ftp'
                            name="site_settings[storage]"  id="ftpStorage">
                            <label class="form-check-label mb-0" for="ftpStorage"></label>
                        </div>
                    </div>

                    <div class="p-3 mt-3 mb-4 bg--danger-light">
                        <p class="text--dark"><span class="bg--danger text-white py-0 px-2 d-inline-block me-2">{{translate("note")}}  :</span>
                            {{trans("default.ftp_note")}}
                        </p>
                    </div>
                    <div class="row">
                        @foreach($ftpSetttings as $ftpKey => $val)
                            <div class="col-xl-6">
                                <div class="form-inner">
                                    <label for="ftp-{{$ftpKey}}">
                                        {{
                                            ucfirst(str_replace('_',' ',$ftpKey))
                                        }}  <small class="text-danger" >*</small>
                                    </label>
                                    <input required type="text" name="site_settings[ftp][{{$ftpKey}}]" id="ftp-{{$ftpKey}}"  value="{{is_demo() ? '@@@' :$val}}" placeholder="**********">
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12 ">
                            <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                                {{translate("Submit")}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
