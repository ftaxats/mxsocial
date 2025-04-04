@php
  $cookie = get_content("content_cookie")->first();
  $file   = $cookie->file->where("type",'cookie_image')->first();
@endphp

<div class="cookie">
    <div class="cookie-content">
            <p>
              {{@$cookie->value->description}}
            </p>
          <div class="cookie-icon">
             <img src='{{imageURL(@$file,"frontend",true,@get_appearance()->cookie->content->images->image->size)}}'
             alt="{{@$file->name}}" />
          </div>
    </div>

    <div class="cookie-action">
        <button class="i-btn btn--primary btn--lg capsuled cookie-control"  data-route='{{route("accept.cookie")}}'>
            {{translate("Accept")}}
        </button>

        <button class="i-btn btn--primary-outline btn--lg capsuled cookie-control" data-route='{{route("reject.cookie")}}'>
            {{translate("Decline")}}
        </button>

    </div>
</div>
