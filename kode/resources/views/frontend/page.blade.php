@extends('layouts.master')
@section('content')

@include("frontend.partials.breadcrumb")
  <section class="pages-wrapper pb-110">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
              <div class="page-content text-editor-content linear-bg">
                  @php echo $page->description @endphp
              </div>
          </div>
      </div>
    </div>
  </section> 
@endsection

