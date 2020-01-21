<section>

    <header id="{{ $doc->path }}">
        <h3>
            @if($doc->metadata['title'] != ''){{ $doc->metadata['title'] }}@else{{$doc->uri}}@endif
        </h3>
    </header>

    @if(isset($doc->authenticated))
        <span class="alert alert-warning">Requires authentication</span>
    @endif

    @if($doc->metadata['description'])
        <p>{!! $doc->metadata['description'] !!}</p>
    @endif

    <section>
        <header id="{{ $doc->path }}-request"><h4>HTTP request</h4></header>
        @foreach($doc->methods as $method)
            <code><b>{{$method}} {{$doc->uri}}</b></code>
        @endforeach

        @if(count($doc->bodyParameters))
            <h4>Body parameters</h4>
            <table class="table table-condensed table-hover table-striped">
                <thead><tr><th>Parameter</th><th>Type</th><th>Status</th><th>Description</th></tr></thead>
                <tbody>
                @foreach($doc->bodyParameters as $attribute => $parameter)
                    <tr>
                        <td><code>{{$attribute}}</code></td>
                        <td>{{$parameter['type']}}</td>
                        <td>@if($parameter['required']) Required @else Optional @endif</td>
                        <td>{!! $parameter['description'] !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        <h4>Query parameters</h4>
        @if(count($doc->queryParameters))
            <table class="table table-condensed table-hover table-striped">
                <thead><tr><th>Parameter</th><th>Type</th><th>Status</th><th>Description</th></tr></thead>
                <tbody>
                @foreach($doc->queryParameters as $attribute => $parameter)
                    <tr>
                        <td><code>{{$attribute}}</code></td>
                        <td>{{$parameter['type']}}</td>
                        <td>@if($parameter['required']) Required @else Optional @endif</td>
                        <td>{!! $parameter['description'] !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p>None.</p>
        @endif
    </section>

    <section>

        <h4>Example requests</h4>
        <ul class="nav nav-tabs">
            <li role="presentation" class="active">
                <a href="#bash-{{ $doc->id }}" aria-controls="profile" role="tab" data-toggle="tab">Bash</a>
            </li>
            <li role="presentation">
                <a href="#javascript-{{ $doc->id }}" aria-controls="profile" role="tab" data-toggle="tab">JavaScript</a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="bash-{{ $doc->id }}">@include('docdocgoose::html.examples.bash')</div>

            <div role="tabpanel" class="tab-pane" id="javascript-{{ $doc->id }}">@include('docdocgoose::html.examples.javascript')</div>
        </div>

        @if(in_array('GET',$doc->methods) || (isset($doc->showresponse) && $doc->showresponse))
            <header id="{{ $doc->path }}-examples"><h4>Example response</h4></header>
            @include('docdocgoose::html.examples.response')
        @endif

    </section>

</section>