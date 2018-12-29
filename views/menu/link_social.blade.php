@foreach($items as $item)
    <a href="{{$item->link()}}" target="_social" data-icon="{{$item->icon_class}}" title="{{$item->title}}" class="symbol"></a>
@endforeach
