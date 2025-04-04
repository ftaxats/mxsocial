@if($loop->index == 0)
    <div class="col-12">
        <div class="blog-style-one">
            <div class="row align-items-center g-3">
                <div class="col-md-7 pe-lg-4">
                    <div class="image">
                        <img src='{{imageURL(@$blog->file,"blog",true)}}' alt="{{@$blog->file->name ?? 'blog-image.jpg'}}"/>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="content">
                       
                        <h3><a href="{{route('blog.details',$blog->slug)}}"> {{limit_words($blog->title,28)}}</a></h3>
                        <p>
                            {{limit_words(strip_tags($blog->description),30)}}
                        </p>
                        <a href="{{route('blog.details',$blog->slug)}}" class="i-btn btn--lg btn--primary capsuled">
                             {{translate("More info")}}
                            <span><i
                                    class="bi bi-arrow-up-right"></i></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="col-lg-4 col-md-6">
        <div class="blog-style-two">
            <div class="date">{{get_date_time($blog->created_at,"F j, Y")}}</div>
            <div class="content">
          
                <h4>
                    <a href="{{route('blog.details',$blog->slug)}}"> {{limit_words($blog->title,28)}}</a>
                </h4>
            </div>
            <a href="{{route('blog.details',$blog->slug)}}" class="i-btn btn--lg btn--white capsuled"> {{translate("More info")}}<span><i
                        class="bi bi-arrow-up-right"></i></span></a>
    
            <div class="image">
                <img src='{{imageURL(@$blog->file,"blog",true)}}'
                                  alt="{{@$blog->file->name ?? 'blog-image.jpg'}}"/>
            </div>
        </div>
    </div>
@endif