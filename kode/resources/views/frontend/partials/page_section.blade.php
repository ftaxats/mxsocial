@if(@$menu->section)
    @foreach(@$menu->section as $section)
            @include('frontend.sections.'.$section)
    @endforeach
@endif