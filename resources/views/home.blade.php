@extends('templates.main')
@section('title', 'SpaceX Stats - SpaceX News, Countdowns, & Fan Community')

@section('content')
<body class="home" ng-controller="homeController" ng-strict-di ng-keydown="keypress($event)">


    @include('templates.header', ['class' => 'no-background'])

    <!-- Main content -->
    <div class="content-wrapper single-page background" id="home" du-scrollspy="home">
        <h1>Welcome</h1>
        <main>
            <div class="app-description gr-4 gr-12@small">
                <p class="exclaim"><a href="/">SpaceX Stats</a></p>
                <p>SpaceX Stats is the first website dedicated entirely to following SpaceX and their missions. Countdown to upcoming launches, read about past missions, watch and follow missions live as they happen, and much more!</p>

                <p>SpaceX Stats is currently in public beta, and as such, some features may not be entirely operational or may appear broken. You can contact me to report problems here.</p>
            </div>
            <div class="app-description gr-4 gr-12@small">
                <p class="exclaim"><a href="/missioncontrol">Mission Control</a></p>

                <p>Coming soon: Subscribe to Mission Control to get exclusive content, mission notifications via SMS and email, participate in the spaceflight fan community, and the ability to precisely locate and find a wealth of media using targeted search.</p>

                <p>Mission Control is currently in private beta and will be releasing in early 2016.</p>
            </div>
            <div class="app-description gr-4 gr-12@small">
                <p class="exclaim"><a href="/live">Live</a></p>

                <p>Using SpaceX Stats Live, you can both watch SpaceX launches in real time and receive live updates pre- and post-mission to keep you informed via the ve. With Imgur and Twitter integration, this is the single best way to be totally connected during a SpaceX launch.</p>

                <p>No more hassles with refreshing antiquated forum pages or having multiple windows. Everything is in one place.</p>
            </div>
            <button class="next-stat" ng-click="goToFirstStatistic()"><i class="fa fa-angle-down fa-3x"></i></button>
        </main>
    </div>

    <!-- Navigation -->
    <ul id="side-navigation" class="hide@small">
        <li class="statistic-holder">
            <a class="link" ng-class="{ 'active' : activeStatistic == null }" ng-click="goHome()"></a>
        </li>
        <li class="statistic-holder" ng-repeat="statistic in statistics">
            <a class="link" ng-class="{ 'active' : statistic == activeStatistic }" ng-click="goToClickedStatistic(statistic)"></a>
        </li>
    </ul>

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
                    <tr>
                        <td>@{{ substatistic.result[0] }}</td>
                        <td>@{{ substatistic.result[1] }}</td>
                    </tr>
                    <tr>
                        <td>@{{ substatistic.unit[0] }}</td>
                        <td>@{{ substatistic.unit[1] }}</td>
                    </tr>
                </table>

                <countdown ng-if="substatistic.display == 'count'" countdown-to="substatistic.result.launch_date_time" specificity="substatistic.result.launch_specificity" type="classic"></countdown>

                <countdown ng-if="substatistic.display == 'interval'" countdown-to="substatistic.result" specificity="7" type="classic" is-paused="{{ true }}" is-visible-when-paused="{{ true }}"></countdown>

                <div ng-if="substatistic.display == 'mission'">
                    <div class="launch-link">
                        <a href="/missions/@{{ substatistic.result.slug }}">Go to Launch</a>
                    </div>

                    <launch-date is-launch-exact="substatistic.result.launch_specificity >= 6" launch-date-time="substatistic.result.launch_date_time"></launch-date>

                    <countdown countdown-to="substatistic.result.launch_date_time" specificity="substatistic.result.launch_specificity" type="classic">
                    </countdown>
                </div>

                <chart ng-if="substatistic.display == 'barchart'" data="substatistic.result" settings="substatistic.settings" type="bar" width="100%" height="100%"></chart>
            </div>

            <p class="description fade-in-out" ng-show="statistic.show" ng-class="{ fadeIn : statistic.fadeInModel, fadeOut : statistic.fadeOutModel }">@{{ statistic.activeSubstatistic.description }}</p>
            <button class="next-stat" ng-click="goToNeighborStatistic($index + 1)"><i class="fa fa-angle-down fa-3x"></i></button>
        </main>
    </div>
</body>
@stop