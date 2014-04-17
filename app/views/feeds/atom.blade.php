{{ '<?xml version="1.0" encoding="utf-8"?>' }}

<feed xmlns="http://www.w3.org/2005/Atom">
    <title>laravelrocks</title>
    <subtitle>Laravel rocks는 Laravel 관련 팁을 모아 놓은 사이트입니다</subtitle>
    <link href="{{ Request::url() }}" rel="self" />
    <updated>{{ Carbon\Carbon::now()->toATOMString() }}</updated>
    <author>
        <name>YongHun Byun</name>
        <uri>http://twitter.com/river</uri>
    </author>
    <id>tag:{{ Request::getHost() }},{{ date('Y') }}:/feed.atom</id>

@foreach($tricks as $trick)
    <entry>
        <title>{{ $trick->title }}</title>
        <link href="{{ route('tricks.show', $trick->slug) }}" />
        <id>{{ $trick->tagUri }}</id>
        <updated>{{ $trick->updated_at->toATOMString() }}</updated>
        <content type="html">{{{ $trick->prettyDescription }}}</content>
    </entry>
@endforeach
</feed>
