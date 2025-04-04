@extends('admin.layouts.master')
@section('content')

    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="d-flex justify-content-md-end justify-content-start">
                    <div class="search-area">
                        <form action="{{route(Route::currentRouteName())}}" method="get">
                            <div class="form-inner">
                                <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search by name or subject')}}">
                            </div>
                            <button class="i-btn btn--sm info">
                                <i class="las la-sliders-h"></i>
                            </button>
                            <a href="{{route('admin.template.list')}}"  class="i-btn btn--sm danger">
                                <i class="las la-sync"></i>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                               #
                            </th>
                            <th scope="col">
                                {{translate('Name')}}
                            </th>
                            <th scope="col">
                                {{translate('Subject')}}
                            </th>
                           
                            <th scope="col">
                                {{translate('Options')}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td data-label="#">
                                    {{$loop->iteration}}
                                </td>
                                
                                <td data-label="{{translate('Name')}}">
                                    {{$template->name}}
                                </td>
                                
                                <td data-label="{{translate('Subject')}}">
                                    {{ limit_words($template->subject,20)}}
                                </td>
                               
                                <td data-label="{{translate('Options')}}">
                                    <div class="table-action">
                                        @if(check_permission('update_template'))
                                           <a  href="{{route('admin.template.edit',$template->uid)}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}"  class="update icon-btn warning"><i class="las la-pen"></i></a>
                                        @else
                                            --
                                        @endif
                                    </div>
                                </td>
                            </tr>
                 
                        @empty
                            <tr>
                                <td class="border-bottom-0" colspan="4">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
@endsection


