<form data-route="{{route('admin.setting.ticket.store')}}"  class="settingsForm"  method="POST" enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{  Arr::get($tab,'title') }}
            </h4>
            <div class="action">
                <button id="add-ticket-option" class="i-btn btn--sm success">
                    <i class="las la-plus me-1"></i>   {{translate('Add More')}}
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Table Foot -->
            <div class="table-container">
                <table class="align-middle">
                    <thead class="table-light ">
                        <tr>
                            <th scope="col">
                                {{translate('Labels')}}
                            </th>
                            <th scope="col">
                                {{translate('Type')}}
                            </th>
                            <th scope="col">
                                {{translate('Mandatory/Required')}}
                            </th>
                            <th scope="col">
                                {{translate('Placeholder')}}
                            </th>
                            <th scope="col">
                                {{translate('Action')}}
                            </th>
                        </tr>
                    </thead>

                    <tbody id="ticketField">
                        @foreach ($ticketSettings as $ticketInput)
                         <tr>
                            <td data-label='{{translate("Label")}}'>
                                <div class="form-inner mb-0">
                                    <input type="text" name="custom_inputs[{{$loop->index}}][labels]"  value="{{$ticketInput['labels']}}">
                                </div>
                            </td>
                            <td data-label='{{translate("Type")}}'>
                                <div class="form-inner mb-0">
                                    @if($ticketInput['default'] == App\Enums\StatusEnum::true->status())
                                        <input disabled type="text" name="custom_inputs[type]"  value="{{$ticketInput['type']}}">
                                        <input type="hidden" name="custom_inputs[{{$loop->index}}][type]"  value="{{$ticketInput['type']}}">
                                    @else
                                    <select class="select2" name="custom_inputs[{{$loop->index}}][type]" >
                                        @foreach(['file','textarea','text','date','email'] as $type)
                                            <option {{$ticketInput['type'] == $type ?'selected' :""}} value="{{$type}}">
                                                {{ucfirst($type)}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </td>
                            <td  data-label='{{translate("Required")}}' >
                                <div class="form-inner mb-0">
                                    @if($ticketInput['default'] == App\Enums\StatusEnum::true->status() && $ticketInput['type'] != 'file' )
                                        <input disabled  type="text" name="custom_inputs[required]"  value="{{$ticketInput['required'] == App\Enums\StatusEnum::true->status()? 'Yes' :'No'}}">
                                        <input hidden  type="text" name="custom_inputs[{{$loop->index}}][required]"  value="{{$ticketInput['required']}}">
                                    @else
                                        <select class="form-select" name="custom_inputs[{{$loop->index}}][required]" >
                                            <option {{$ticketInput['required'] == App\Enums\StatusEnum::true->status() ?'selected' :""}} value="{{App\Enums\StatusEnum::true->status()}}">
                                                {{translate('Yes')}}
                                            </option>
                                            <option {{$ticketInput['required'] == App\Enums\StatusEnum::false->status() ?'selected' :""}} value="{{App\Enums\StatusEnum::false->status()}}">
                                                {{translate('No')}}
                                            </option>
                                        </select>
                                    @endif
                                </div>
                            </td>
                            <td  data-label="{{translate('Placeholder')}}">
                                <div class="form-inner mb-0">
                                    <input type="text" name="custom_inputs[{{$loop->index}}][placeholder]"  value="{{$ticketInput['placeholder']}}">
                                </div>
                                <input   type="hidden" name="custom_inputs[{{$loop->index}}][default]"  value="{{$ticketInput['default']}}">
                                <input   type="hidden" name="custom_inputs[{{$loop->index}}][multiple]"  value="{{$ticketInput['multiple']}}">
                                <input   type="hidden" name="custom_inputs[{{$loop->index}}][name]"  value="{{$ticketInput['name']}}">
                            </td>
                            <td data-label="{{translate('Option')}}">
                                @if($ticketInput['default'] == App\Enums\StatusEnum::true->status())
                                    --
                                @else
                                    <div>
                                        <a href="javascript:void(0);" class="pointer icon-btn danger delete-option">
                                            <i class="las la-trash-alt"></i>
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-20">
                <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                    {{translate("Submit")}}
                </button>
            </div>
        </div>
    </div>
</form>