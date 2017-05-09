@extends('templates.main')
@section('title', 'SpaceX Stats - SpaceX News, Countdowns, & Fan Community')

@section('content')
<body class="home" ng-controller="homeController" ng-strict-di ng-keydown="keypress($event)">

    @if (Auth::isAdmin())
        @include('templates.header', ['class' => 'no-background'])
    @endif

    <!-- Statistics -->
    <div class="content-wrapper single-page background" ng-repeat="statistic in statistics" id="@{{ statistic.camelCaseType }}" du-scrollspy="@{{ substatistic.camelCaseType }}" href="#@{{ statistic.camelCaseType }}">

        <h1 class="fade-in-out" ng-show="statistic.show" ng-class="{ fadeIn : statistic.fadeInModel, fadeOut : statistic.fadeOutModel }">@{{ statistic.activeSubstatistic.full_title }}</h1>

        <main class="invert">
            <button class="previous-stat" ng-click="goToNeighborStatistic($index - 1)"><i class="fa fa-angle-up fa-3x"></i></button>

            <nav class="in-page">
                <ul class="container">
                    <li class="gr-2" ng-repeat="substatistic in statistic.substatistics" ng-class="{ 'active':statistic.activeSubstatistic == substatistic }" ng-click="statistic.changeSubstatistic(substatistic)">@{{ substatistic.name }}</li>
                </ul>
            </nav>

            <div class="hero fade-in-out statistic" ng-repeat="substatistic in statistic.substatistics" ng-show="statistic.activeSubstatistic == substatistic && statistic.show" ng-class="{ fadeIn : statistic.fadeInModel, fadeOut : statistic.fadeOutModel }">

                <table class="single" ng-if="substatistic.display == 'single'">
                    <tr class="value">
                        <td>@{{ substatistic.result }}</td>
                    </tr>
                    <tr class="unit">
                        <td>@{{ substatistic.unit }}</td>
                    </tr>
                </table>

                <table class="double" ng-if="substatistic.display == 'double'">
                    <tr class="value">
                        <td>@{{ substatistic.result[0] }}</td>
                        <td>@{{ substatistic.result[1] }}</td>
                    </tr>
                    <tr class="unit">
                        <td>@{{ substatistic.unit[0] }}</td>
                        <td>@{{ substatistic.unit[1] }}</td>
                    </tr>
                </table>

                <countdown ng-if="substatistic.display == 'count'" countdown-to="substatistic.result" type="classic"></countdown>

                <countdown ng-if="substatistic.display == 'interval'" countdown-to="substatistic.result" specificity="7" type="interval"></countdown>

                <div ng-if="substatistic.display == 'mission'">
                    <countdown countdown-to="substatistic.result.launch_date_time" specificity="substatistic.result.launch_specificity" type="classic">
                    </countdown>

                    <launch-date launch-specificity="substatistic.result.launch_specificity" launch-date-time="substatistic.result.launch_date_time"></launch-date>

                    <div class="launch-link">
                        <a href="/missions/@{{ substatistic.result.slug }}">Go to Launch</a>
                    </div>
                </div>

                <chart ng-if="substatistic.display == 'barchart'" data="substatistic.result.values" settings="substatistic.result" type="bar" width="100%" height="100%"></chart>

                <table class="gesture" ng-if="substatistic.display == 'gesture'">
                    <tr class="value">
                        <td>@{{ substatistic.result }}</td>
                    </tr>
                    <tr class="unit">
                        <td>@{{ substatistic.unit }}</td>
                    </tr>
                </table>
            </div>

            <p class="description fade-in-out" ng-show="statistic.show" ng-class="{ fadeIn : statistic.fadeInModel, fadeOut : statistic.fadeOutModel }">@{{ statistic.activeSubstatistic.description }}</p>
            <button class="next-stat" ng-click="goToNeighborStatistic($index + 1)"><i class="fa fa-angle-down fa-3x"></i></button>
        </main>
    </div>

    <footer class="content-wrapper background" id="footer" du-scrollspy="home">
        <main>
            <p class="description">
                Photos on this page courtesy SpaceX, & NASA. All rights maintained by the respective owners. <br/>
                This site is fan-run and not affiliated with SpaceX in any way. For official information and news, please visit <a href="http://spacex.com" title="Official SpaceX website">spacex.com</a><br />
                Original site concept and design by <a href="https://www.reddit.com/user/echologic" title="Echologic's Reddit profile">/u/echologic</a>, now temporarily rehosted by <a href="https://www.reddit.com/user/brandtamos" title="Brandtamos' Reddit profile">/u/brandtamos</a>
            </p>
        </main>
    </footer>
</body>
@stop
