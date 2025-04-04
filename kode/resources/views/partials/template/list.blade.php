@if(@$template_category)
    <button @if($template_category->parent)   data-category-id ="{{$template_category->parent->id}}" @endif    type="button" class="back-to-prompt d-flex align-items-center gap-2 p-2 border rounded-pill lh-1 pointer template-category">
        <i class="bi bi-arrow-left-circle-fill fs-18"></i>
        <span class="fw-500">
             {{
                translate('Back')
             }}  @if($template_category->parent)
                  - {{ $template_category->parent->title}}
                 @endif
        </span>
    </button>
@endif



@if(@$categories &&  @$categories->count() > 0 )
    <div class="categories-list">
        @foreach ($categories  as $category)
            <div class="category-item category-select template-category" data-category-id ="{{$category->id}}"
              @if($category->parent)
                  data-parent-id ="{{$category->parent->id}}"
              @endif>
                <div class="icon"><i class="{{$category->icon}}"></i></div>
                <h5>{{ $category->title}}</h5>
            </div>
        @endforeach
    </div>
@else
   @if(!@$template_category)
      @include('admin.partials.not_found',['custom_message' => translate('No categories found')])
   @endif
@endif


@if(@$custom_templates && @$template_category  && @$template_category->count() > 0)
    <div class="templates">
        <div class="d-flex align-items-center gap-2 lh-1 text--primary" >
            <i class="{{$template_category->icon}} fs-18"></i>
            <span class="fw-600">  {{ $template_category->title}}</span>
        </div>

        @if($custom_templates->count() > 0)
            <ul class="template-list mt-3">
                @forelse ($custom_templates as $template )
                    <li class="template select-template" role="button" data-template-id = {{$template->id}}>
                        <h6>{{ $template->name }}
                        </h6>
                        <p>{{ limit_words($template->description , 50)}}</p>
                    </li>
                @empty
                @endforelse
            </ul>
        @else
            @include('admin.partials.not_found',['custom_message' => translate('No templates found')])
        @endif
    </div>
@endif
