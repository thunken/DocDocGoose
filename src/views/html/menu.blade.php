<ul class="list-group">
    @foreach($versions as $version)
        <li class="list-group-item">
            <a href="#{{ $version->getPath() }}" title="{{ $version->getName() }}">{{ $version->getName() }}</a>
            <ul class="list-group">
            @foreach($version as $group)
                <li class="list-group-item">
                    <a href="#{{ $group->getPath() }}" title="{{ $group->getName() }}">{{ $group->getName() }}</a>
                    <ul class="list-group">
                        @foreach($group as $doc)
                            <li class="list-group-item">
                                <a href="#{{ $doc->path }}" title="#{{ $doc->title }}">{{ $doc->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
            </ul>
        </li>
    @endforeach
</ul>