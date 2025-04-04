
@php


 $class = 'card-loader content-loader d-none';


 if(@$customer_class){
    $class =   $class." ".@$customer_class;
 }

@endphp


<div class="{{$class}}"    >
    <div class="spinner">
        <div class="lds-roller">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>