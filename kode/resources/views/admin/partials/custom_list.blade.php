<ul class="custom-info-list list-group-flush">
        @foreach ($lists as $k => $list)
                @if(@$db_list)
    
                    <li><span>{{ translate(ucfirst($k)) }} :</span>
                            @if ($list->type == 'file')
                                @php
                                    $file = $report
                                                    ->file
                                                    ->where('type', $k)
                                                    ->first();
                    
                                @endphp
                                <div class="custom-profile">
                            
                                    <div class="image-v-preview" title="{{ k2t($k) }}">
                                        <img src="{{imageURL($file,@$file_path,true)}}" alt="{{ ucfirst($k).'.jpg' }}">
                                    </div>
                                
                                </div>
                            @else
                                <span>{{ $list->field_name }}</span>
                            @endif
                    </li>
                @else

                    <li>
                        <span>{{ Arr::get($list, 'title') }}:</span> 
                        @php 
                            $value = Arr::get($list,'value') ;
                        @endphp
                        @if(Arr::has($list,'href') && Arr::get($list,'href') )
                                <a href='{{Arr::get($list,"href")}}'>
                                {{   $value }}
                            </a>
                        @else
                            @if(Arr::has($list,'is_html'))
                                @php echo ($value) @endphp
                            @else
                                <span>
                                    {{   $value }}
                                </span>
                            @endif
                        @endif
                    </li>
                @endif
        @endforeach
</ul>