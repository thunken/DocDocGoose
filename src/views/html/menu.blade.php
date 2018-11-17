@foreach($groups as $group)
    <ul class="list-group">
        <li class="list-group-item"><a href="#{{ $group->getPath() }}" title="{{ $group->getName() }}">{{ $group->getName() }}</a></li>
        @foreach($group as $doc)
            <ul class="list-group">
                <li class="list-group-item"><a href="#{{ $doc->path }}" title="#{{ $doc->title }}">{{ $doc->title }}</a></li>
            </ul>
        @endforeach
    </ul>
@endforeach

