@foreach($versions as $version)
    <section>
        <header id="{{ $version->getPath() }}">
            <h1>{{ $version->getName() }}</h1>
        </header>

        @foreach($version as $group)
            @include('docdocgoose::html.group')
        @endforeach

    </section>
@endforeach
