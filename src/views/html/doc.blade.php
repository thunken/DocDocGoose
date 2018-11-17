<section>
    <header id="{{ $doc->path }}">
        <h3>
            @if($doc->title != ''){{ $doc->title}}@else{{$doc->uri}}@endif
        </h3>
    </header>

    @if($doc->authenticated)
        <span class="alert alert-info">Require authentication</span>
    @endif

    @if($doc->description)
        <p>{!! $doc->description !!}</p>
    @endif

    <h4 class="">HTTP Request</h4>
    @foreach($doc->methods as $method)
        <div><b><em>{{$method}} {{$doc->uri}}</em></b></div>
    @endforeach

    @if(count($doc->bodyParameters))
        <h5>Body Parameters</h5>
        <table class="table table-condensed table-hover table-striped">
            <thead><tr><th>Parameter</th><th>Type</th><th>Status</th><th>Description</th></tr></thead>
            <tbody>
            @foreach($doc->bodyParameters as $attribute => $parameter)
                <tr>
                    <td>{{$attribute}}</td>
                    <td>{{$parameter['type']}}</td>
                    <td>@if($parameter['required']) required @else optional @endif</td>
                    <td>{!! $parameter['description'] !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if(count($doc->queryParameters))
        <h5>Query Parameters</h5>
        <table class="table table-condensed table-hover table-striped">
            <thead><tr><th>Parameter</th><th>Status</th><th>Description</th></tr></thead>
            <tbody>
            @foreach($doc->queryParameters as $attribute => $parameter)
                <tr>
                    <td>{{$attribute}}</td>
                    <td>@if($parameter['required']) required @else optional @endif</td>
                    <td>{!! $parameter['description'] !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <h5>Example Requests</h5>
    <blockquote>
        <h6>Bash</h6>
        <code class="language-bash">
            curl -X {{$doc->methods[0]}} {{$doc->methods[0] == 'GET' ? '-G ' : ''}}"{{ trim(config('app.docs_url') ?: config('app.url'), '/')}}/{{ ltrim($doc->uri, '/') }}" @if(count($doc->headers))\
            @foreach($doc->headers as $header => $value)
                -H "{{$header}}: {{$value}}"@if(! ($loop->last) || ($loop->last && count($doc->bodyParameters))) \
                @endif
            @endforeach
            @endif
            @foreach($doc->bodyParameters as $attribute => $parameter)
                -d "{{$attribute}}"="{{$parameter['value'] === false ? "false" : $parameter['value']}}" @if(! ($loop->last))\
                @endif
            @endforeach
        </code>

        <h6>Javascript</h6>
        <code class="language-javascript">
            var settings = {
            "async": true,
            "crossDomain": true,
            "url": "{{ rtrim(config('app.docs_url') ?: config('app.url'), '/') }}/{{ ltrim($doc->uri, '/') }}",
            "method": "{{$doc->methods[0]}}",
            @if(count($doc->bodyParameters))
                "data": {!! str_replace("\n}","\n    }", str_replace('    ','        ',json_encode(array_combine(array_keys($doc->bodyParameters), array_map(function($param){ return $param['value']; },$doc->bodyParameters)), JSON_PRETTY_PRINT))) !!},
            @endif
            "headers": {
            @foreach($doc->headers as $header => $value)
                "{{$header}}": "{{$value}}",
            @endforeach
            }
            }

            $.ajax(settings).done(function (response) {
            console.log(response);
            });
        </code>
    </blockquote>

    <h4>Example response</h4>
    <blockquote>
        @if(in_array('GET',$doc->methods) || (isset($doc->showresponse) && $doc->showresponse))
            <code class="language-json">
                @if(is_object($doc->response) || is_array($doc->response))
                    {!! json_encode($doc->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
                @else
                    {!! json_encode(json_decode($doc->response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
                @endif
            </code>
        @endif
    </blockquote>
</section>