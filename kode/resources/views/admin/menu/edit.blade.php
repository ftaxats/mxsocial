@extends('admin.layouts.master')

@push('styles')
   <style nonce="{{ csp_nonce() }}">
         .dragable-section .section-remove{
            display: none !important;
         }

         .dragable-section .cog-section{
            display: block !important;
         }

         .dropable-section .section-remove{
            display: block !important;
         }

         .dropable-section .cog-section{
            display: none !important;
         }

         .drop-here{
            position: relative;
            border: 1px dashed #dadce0;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100px;
            width: 100%;
            h6{
                font-size: 18px;
                margin-bottom: 0;
            }
        }
   </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-xl-7">
            <form action="{{route('admin.menu.update')}}" class="add-listing-form" enctype="multipart/form-data" method="post">
                    @csrf
                <input hidden type="text" name="id" value="{{$menu->id}}">
                <div class="i-card-md">
                    <div class="card--header">
                        <h4 class="card-title">
                            {{translate('Basic Information')}}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label class="form-label" for="name">
                                        {{translate('Name')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" placeholder='{{translate("Enter name")}}'
                                        value="{{$menu->name}}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label class="form-label" for="url">
                                        {{translate('URL')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="url" id="url" placeholder='{{translate("Enter URL")}}'
                                        value="{{$menu->url}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-inner">
                                    <label for="serial_id">
                                        {{translate('Serial Id')}}  <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="serial_id" value="{{$menu->serial_id}}" >
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-inner ">
                                    <label class="me-2">
                                        {{translate("Visible In")}}
                                    </label>
                                    @foreach (App\Enums\MenuVisibilty::toArray() as $k => $v )
                                        <input id="{{ $k }}" @if($menu->menu_visibility == $v ) checked  @endif value="{{ $v }}" class="form-check-input" name="menu_visibility" type="radio">
                                        <label for="{{ $k }}" class="form-check-label me-2">
                                            {{translate($k)}}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <ul class="dropable-section mt-4">
                                <li class="drop-here mb-4">
                                    <h6>{{ translate('Drop here') }}</h6>
                                </li>

                                @if($menu->section)
                                    @foreach ($menu->section as $key)
                                        <li class="alert border fade show alert-with-icon pointer  moveable-section">
                                            <input type="hidden" name="section[]" value="{{$key}}">
                                            <i class="las la-check-circle"></i>
                                            <p>
                                                {{k2t($key)." section"}}
                                            </p>
                                            <a href="javascript:void(0)" class="i-btn btn--sm danger ms-auto section-remove">
                                                <i class="las la-times-circle"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                            <div class="col-12">
                                <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                                    {{translate("Submit")}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-xl-5">
            <div class="i-card-md">
                <div class="card--header">
                    <h4 class="card-title">
                        {{translate('Sections')}}
                    </h4>
                </div>
                <div class="card-body">
                    <ul class="dragable-section">
                        @foreach (get_appearance(true) as $key => $appearance )
                            @if (isset($appearance['builder']) && $appearance['builder'] && !@$appearance['no_selection'])
                                <li class="alert border fade show alert-with-icon pointer  moveable-section">
                                    <input type="hidden" name="section[]" value="{{$key}}">
                                    <i class="las la-check-circle"></i>
                                    <p>
                                        {{k2t(Arr::get($appearance,'name',""))}}
                                    </p>
                                    <a href="javascript:void(0)" class="i-btn btn--sm danger ms-auto section-remove">
                                        <i class="las la-times-circle"></i>
                                    </a>
                                    <a href='{{route("admin.appearance.list",$key)}}' class="i-btn btn--sm success cog-section ms-auto">
                                        <i class="las la-cog"></i>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
   </div>
@endsection

@push('script-include')
   <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/jquery-sortable.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       "use strict";
       $(".dropable-section").sortable({
            group: 'no-drop',
            handle: '.moveable-section',
            onDragStart: function ($item, container, _super) {
                    if(!container.options.drop){
                        $item.clone().insertAfter($item);
                    }
                    _super($item, container);
                }
        });

        $(".dragable-section").sortable({
            group: 'no-drop',
            drop: false
        });

        $(document).on('click',".section-remove",function(e){
            e.preventDefault()
            $(this).parent().remove();
        })

	})(jQuery);
</script>
@endpush




