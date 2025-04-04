@extends('admin.layouts.master')
@push('style-include')
<link  nonce="{{ csp_nonce() }}" rel="stylesheet" href="{{asset('assets/global/css/summnernote.css')}}">
@endpush
@section('content')

<form action="{{route('admin.blog.update')}}" class="add-listing-form" enctype="multipart/form-data" method="post">
    @csrf
    <input hidden value="{{$article->id}}" type="text" name="id">
    <div class="row g-4">
        @php
            $col = @site_settings('site_seo') == App\Enums\StatusEnum::true->status() ? 8 :12;
        @endphp
        <div class="col-xl-{{$col}}">
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
                                <label for="title">
                                    {{translate('Title')}} <small class="text-danger">*</small>
                                </label>
                                <input placeholder="{{translate('Enter Title')}}" id="title"  required type="text" name="title" value="{{$article->title}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-inner">
                                <label for="slug">
                                    {{translate('slug')}}
                                </label>
                                <input placeholder="{{translate('Enter Slug')}}" id="slug" type="text" name="slug" value="{{$article->slug}}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-inner">
                                <label for="description"> {{translate('Description')}} <small class="text-danger">*</small></label>
                                <textarea id="description" class="summernote" name="description"  cols="30" rows="5">@php echo ($article->description) @endphp</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-inner">
                                <label for="image"> {{translate('Image')}} <small class="text-danger">({{config("settings")['file_path']['blog']['size']}})</small></label>
                                <input data-size = "120x120" id="image" name="image" type="file" class="preview">
                                <div class="mt-2 payment-preview image-preview-section mb-2" >
                                    <img   src='{{imageURL(@$article->file,"blog",true)}}' alt="{{@$article->file->name}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-4">
                            <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                                {{translate("Submit")}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @includeWhen(@site_settings('site_seo') == App\Enums\StatusEnum::true->status(),'admin.partials.seo',['model' =>$article])

    </div>
</form>

@endsection
@push('script-include')

<script nonce="{{ csp_nonce() }}"  src="{{asset('assets/global/js/summernote.min.js')}}"></script>
<script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/editor.init.js')}}"></script>

@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

            $(".select2").select2({
			   placeholder:"{{translate('Select Category')}}",
	     	})
            $(".selectMeta").select2({
                placeholder:"{{translate('Enter Keywords')}}",
                tags: true,
                tokenSeparators: [',']
	     	})
	})(jQuery);
</script>
@endpush
