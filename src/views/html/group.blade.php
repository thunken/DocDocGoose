<section>
    <header><h2 id="{{ $group->getPath() }}">{{ $group->getName() }}</h2></header>

    @foreach($group as $doc)
        @include('docdocgoose::html.doc')
    @endforeach
</section>