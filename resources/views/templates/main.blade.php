<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>@yield('title') | SpaceX Stats</title>

    <meta property="og:image" content="/redditthumb.jpg" />

    <!-- Iconography -->
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-title" content="SpaceX Stats">
    <meta name="application-name" content="SpaceX Stats">
    <meta name="msapplication-TileColor" content="#ccac55">
    <meta name="msapplication-TileImage" content="/mstile-144x144.png">
    <meta name="theme-color" content="#ccac55">

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/styles.css" />

    <!-- JS -->
        <!-- jQuery& jQuery plugins -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        <script src="//cdn.jsdelivr.net/stickynavbar.js/1.3.2/jquery.stickyNavbar.min.js"></script>

        <!-- Angular -->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.7/angular-animate.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0-beta.1/angular-sanitize.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-scroll/1.0.0/angular-scroll.js"></script>
        <script src="/js/angular-ui-tree.js"></script>
        <script src="/js/angular-datepicker.js"></script>
        <script src="/js/angular-credit-cards.js"></script>

        <!-- Moment.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.4.0/moment-timezone-with-data.js"></script>

        <!-- d3.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.js"></script>
        <script src="/js/d3-tip.js"></script>

        <!-- jstz & dropzone -->
        <script src="/js/jstz-1.0.4.min.js"></script>
        <script src="/js/dropzone.js"></script>

        <!-- Frontend App -->
        <script src="/js/app.js"></script>
        <script src="/js/spacexstatsApp.js"></script>
    
        <script>
            $(document).ready(function() {
                // Mobile left hand side drawer
                var mobileNavigation = $('.nav-mobile');
                var contentToPush = $('body');

                $('i.mobile-hamburger').on('click', function() {
                    console.log('click');
                    contentToPush.toggleClass('nav-open');
                });

                // Stickybar
                $('nav.sticky-bar').stickyNavbar({
                    selector: 'li',
                    startAt: 0
                });
            });
        </script>

    <!-- Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Source+Code+Pro' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

    @if(request()->is('live'))
        <base target="_blank">
    @endif

    <script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-92312771-1', 'auto');
		ga('send', 'pageview');

	</script>
</head>
@yield('content')
</html>