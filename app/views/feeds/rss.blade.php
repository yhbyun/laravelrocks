{{ '<?xml version="1.0" encoding="utf-8"?>' }}

<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
    <channel>
        <title>laravelrocks</title>
        <link>http://www.laravelrocks.com</link>
        <atom:link href="{{ Request::url() }}" rel="self"></atom:link>
        <description>Laravel rocks는 Laravel 관련 팁을 모아 놓은 사이트입니다</description>
        <language>ko-kr</language>
        <lastBuildDate>{{ Carbon\Carbon::now()->toRSSString() }}</lastBuildDate>

@foreach($tricks as $trick)
        <item>
            <title>{{ $trick->title }}</title>
            <link>{{ route('tricks.show', $trick->slug) }}</link>
            <guid>{{ route('tricks.show', $trick->slug) }}</guid>
            <description><![CDATA[{{{ $trick->prettyDescription }}}]]></description>
            <pubDate>{{ $trick->created_at->toRSSString() }}</pubDate>
        </item>
@endforeach
    </channel>
</rss>
