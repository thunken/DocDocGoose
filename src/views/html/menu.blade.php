@foreach($versions as $version)
    <li id="version-{{ $version->getPath() }}">
        <a href="#{{ $version->getPath() }}" title="{{ $version->getName() }}" class="scroll-to">
            <span>{{ $version->getName() }}</span>
        </a>
        <a data-toggle="collapse" href="#collapse-{{ $version->getPath() }}" class="pull-right">
            <span class="caret"></span>
        </a>
        <ul id="collapse-{{ $version->getPath() }}" class="collapse in">
            @foreach($version as $group)
                <li>
                    <a href="#{{ $group->getPath() }}" title="{{ $group->getName() }}" class="scroll-to">
                        <span>{{ $group->getName() }}</span>
                    </a>
                    <a data-toggle="collapse" href="#collapse-{{ $group->getPath() }}" class="pull-right" data-parent="#collapse-{{ $version->getPath() }}">
                        <span class="caret"></span>
                    </a>
                    <ul id="collapse-{{ $group->getPath() }}" class="collapse">
                        @foreach($group as $doc)
                            <li>
                                <a href="#{{ $doc->path }}" title="{{ $doc->metadata['title'] }}" class="scroll-to">{{ $doc->metadata['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </li>
@endforeach