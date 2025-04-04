<div class="search-action-area">
    <div class="row g-3">
        <form hidden id="bulkActionForm" action='{{route("admin.appearance.bulk")}}' method="post">
            @csrf
            <input type="hidden" name="bulk_id" id="bulkid">
            <input type="hidden" name="value" id="value">
            <input type="hidden" name="type" id="type">
        </form>

        @if(check_permission('update_frontend'))
            <div class="col-md-6 d-flex justify-content-start gap-2">
                <div class="i-dropdown bulk-action mx-0 d-none">
                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="las la-cogs fs-15"></i>
                    </button>
                    <ul class="dropdown-menu">
                        @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                            <li>
                                <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @php
                   $addOption = 1;

                   if(@$appearance->name == 'banner' && 6 <  @$appearance_elements->count()){
                         $addOption = 0;
                   }
                @endphp

                @if($addOption == 1 )
                    <a href="javascript:void(0)"  class="i-btn btn--sm success create">
                        <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                    </a>
                @endif
            </div>
        @endif
        <div class="col-md-6 d-flex justify-content-end">
            <div class="search-area">
                <div class="search">
                    <div class="form-inner">
                        <input name="search" class="section-search"  type="search" placeholder="{{translate('Search here')}}">
                    </div>
                    <button class="i-btn btn--sm info section-search-btn">
                        <i class="las la-sliders-h"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-container position-relative">
    @include('admin.partials.loader')
    <table>
        <thead>
            <tr>
                <th scope="col">
                   @if(check_permission('update_frontend'))
                      <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                    @endif#
                </th>

                @foreach($appearance->element as $k => $element)
                    @if($k !='modal' && $element!= 'select'  )
                            <th scope="col">{{ translate(k2t($k)) }}</th>
                    @endif
                @endforeach

                <th scope="col">{{translate('Status')}}</th>
                <th scope="col">{{translate('Action')}}</th>
            </tr>
        </thead>
        <tbody class="custom-tbody">
            @forelse ($appearance_elements as $appearance_element)
                <tr>
                    <td data-label="#">
                        @if( check_permission('update_frontend'))

                        <input  type="checkbox" value="{{$appearance_element->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$appearance_element->id}}" />

                        @endif
                        {{$loop->iteration}}
                    </td>
                    @foreach($appearance->element as $k => $element)
                        @if($k !='modal' && $k != 'select' )
                           <td data-label="{{translate(k2t($k))}}">
                                @if($k == 'images')
                                <div class="avatar-group">
                                       @foreach ($element as $imK => $imType)

                                            @php
                                               $file =  $appearance_element->file?->where('type', $imK)->first()
                                            @endphp

                                            <div class="avatar-group-item">

                                                    <img data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{k2t($imK)}}"  class="rounded-circle avatar-md" src='{{imageURL(@$file,"frontend",true,$imType->size)}}' alt="{{@$file->name}}">

                                            </div>
                                        @endforeach
                                </div>

                                @else
                                    @if($element == 'icon')
                                        <i class="@php echo ($appearance_element?->value?->$k)  @endphp" ></i>
                                    @else
                                        @if($k == 'rating')
                                           <span class="i-badge capsuled info">
                                             {{$appearance_element->value->$k}}<i class="las la-star"></i>
                                          </span>
                                        @else
                                           {{@limit_words(strip_tags($appearance_element->value->$k),20)}}
                                        @endif
                                    @endif
                                @endif
                           </td>
                        @endif
                    @endforeach

                    <td data-label='{{translate("Status")}}'>
                        <div class="form-check form-switch switch-center">
                            <input {{!check_permission("update_frontend") ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                data-column="status"
                                data-route="{{ route('admin.appearance.update.status') }}"
                                data-status="{{ $appearance_element->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                data-id="{{$appearance_element->uid}}" {{$appearance_element->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                            id="status-switch-{{$appearance_element->id}}" >
                            <label class="form-check-label" for="status-switch-{{$appearance_element->id}}"></label>
                        </div>
                    </td>
                    <td data-label='{{translate("Action")}}'>
                        <div class="table-action">
                            @if(check_permission('update_frontend') )
                                @php
                                    $files = [];
                                    if($appearance->element->modal && @$appearance->element->images){
                                        foreach (@$appearance->element->images as $imKey => $imVal) {
                                            $file     =  $appearance_element->file?->where('type', $imKey)->first();
                                            $files[]  =  imageURL(@$file,"frontend",true,$imVal->size);
                                        }
                                    }
                                @endphp

                                @if(@$appearance->child_section)
                                       <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('View Details Section')}}" href="{{route('admin.appearance.list',['parent' =>$appearance_element->id ,'key' => @$appearance->child_section])}}"  class=" fs-15 icon-btn info"><i class="las la-eye"></i></a>
                                @endif

                                <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}" data-id ="{{$appearance_element->id}}"  data-files ="{{collect($files)}}" href="javascript:void(0)" data-appearance ="{{collect($appearance_element->value)}}" class="update fs-15 icon-btn warning"><i class="las la-pen"></i></a>
                                <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}"  title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('admin.appearance.destroy',$appearance_element->id)}}" class="pointer delete-item icon-btn danger">
                                    <i class="las la-trash-alt"></i>
                                </a>
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
