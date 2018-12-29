@foreach($items as $item)
    <a class="text-uppercase" href="{{$item->link()}}">{{$item->title}}</a>
@endforeach
