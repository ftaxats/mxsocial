
@foreach ($cards as $card)
  @php $card = array_to_object($card) @endphp
  
    <div class="{{ $card->class }}">
        <div class="i-card-sm style-2 {{$card->bg}}">
            <div class="card-info">
                <h5 class="title">
                    {{$card->title}}
                </h5>
                <h3>  {{$card->total}} </h3>
                @if(@$card->url)
                    <a href="{{$card->url}}" class="mt-2 i-btn btn--outline btn--sm">
                        {{translate("View All")}}
                    </a>
                @endif
            </div>
            <div class="icon">
                 @php echo ($card->icon) @endphp
            </div>
        </div>
    </div>
    
@endforeach