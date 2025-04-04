@extends('layouts.master')
    @section('content')
      @include('frontend.sections.banner')
    @include('frontend.partials.page_section')
@endsection
@section('modal')
    @include('modal.plan_subscribe')
@endsection
