<div class="col-xl-4">
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{translate('SEO Settings')}}
            </h4>
        </div>
      
        <div class="card-body">                                               
            <div class="form-inner">                                               
                <label class="form-label" for="metaTitle">
                    {{translate('Meta Title')}}
                </label>
                <input type="text" name="meta_title" id="metaTitle"  placeholder='{{translate("Enter Title")}}'
                    value='{{@$model->meta_title?? old("meta_title") }}'>
            </div> 
            <div class="form-inner">                                                
                <label class="form-label" for="meta_description">
                    {{translate('Meta Description')}} 
                </label>
                <textarea  placeholder='{{translate("Enter Description")}}' id="meta_description"  name="meta_description"  cols="30" rows="5">{{@$model->meta_description??old("meta_description") }}</textarea>
            </div>
            <div class="form-inner">
                <label for="meta_keywords"> 
                    {{translate('Meta Keywords')}}  
                </label>
                @php
                   $metaKeywords = @$model->meta_keywords ?? old("meta_keywords");
                @endphp
                <select name="meta_keywords[]" multiple id="meta_keywords"  class="selectMeta" >
                    @if($metaKeywords)
                        @foreach ($metaKeywords as $keyword )
                            <option selected value="{{$keyword}}">
                                {{$keyword}}
                            </option>
                        @endforeach      
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>