<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="https://www.facebook.com/2008/fbml">
    <head>
        @section('description', 'Laravel rocks는 Laravel 관련 팁을 모아 놓은 사이트입니다')
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta property="og:title" content="Laravel 팁" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="http://laravelrocks.com" />
        <meta property="og:image" content="http://laravelrocks.com/img/logo.png" />
        <meta property="og:site_name" content="laravelrocks.com" />
        <meta property="og:description" content="@yield('description')" />
        <meta name="description" content="@yield('description')">
        <meta name="author" content="YongHun Byun, @river">
        <title>@yield('title') | laravelrocks.com</title>
        <link rel="shortcut icon" href="{{ URL::asset('img/favicon.ico') }}">
        {{{ stylesheet_link_tag('application') }}}
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        @yield('styles')
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.2.0/respond.js"></script>
        <![endif]-->
    </head>

    <body>
        <div id="wrap">
            @include('partials.navigation')
            @yield('content')
        </div>

        @include('partials.footer')

        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '{{ Config::get("config.analytics_property_id") }}', 'auto');
        ga('send', 'pageview');

        </script>
        {{{ javascript_include_tag('application') }}}
        @yield('scripts')
        <script type="text/javascript">
            var disqus_shortname = '{{ Config::get("config.disqus_shortname") }}';
            (function(){var e=document.createElement("script");e.async=true;e.type="text/javascript";e.src="//"+disqus_shortname+".disqus.com/count.js";(document.getElementsByTagName("HEAD")[0]||document.getElementsByTagName("BODY")[0]).appendChild(e)})()

            var _urq = _urq || [];
            _urq.push(['initSite', 'd4225c99-647a-49eb-87cd-5459e84a3273']);
            (function() {
                var ur = document.createElement('script'); ur.type = 'text/javascript'; ur.async = true;
                ur.src = ('https:' == document.location.protocol ? 'https://cdn.userreport.com/userreport.js' : 'http://cdn.userreport.com/userreport.js');
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ur, s);
            })();
        </script>
    </body>
</html>
