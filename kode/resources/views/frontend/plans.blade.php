@extends('layouts.master')
@section('content')

@include("frontend.partials.breadcrumb")

    <section class="plan-detail pb-110">
        <div class="container">
            @include("frontend.partials.plan_component")
        </div>
    </section>

@include('frontend.partials.page_section')

@endsection

@section('modal')
    @include('modal.plan_subscribe')
@endsection