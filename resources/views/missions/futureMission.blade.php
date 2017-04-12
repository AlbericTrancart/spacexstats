@extends('templates.main')
@section('title', $mission->name)

@section('content')
    <body class="future-mission">

    @include('templates.header', ['backgroundImage' => $mission->present()->featuredImageUrl()])

        <div class="content-wrapper" ng-controller="futureMissionController" ng-strict-di>
            <h1>{{ $mission->name }}</h1>
            <main ng-cloak>
                <nav class="in-page sticky-bar">
                    <ul class="container">
                        <li class="gr-1">
                            <a href="#countdown">Countdown</a>
                        </li>
                        <li class="gr-1">
                            <a href="#details">Details</a>
                        </li>
                        <li class="gr-1">
                            <a href="#images">Images</a>
                        </li>
                        <li class="gr-1">
                            <a href="#timeline">Timeline</a>
                        </li>
                        <li class="gr-1">
                            <a href="#articles">Articles</a>
                        </li>

                        <li class="gr-1 float-right">
                            @if ($mission->status == 'In Progress')
                                <span class="status in-progress"><i class="fa fa-check"></i> In Progress</span>
                            @else
                                <span class="status upcoming"><i class="fa fa-cross"></i> Upcoming</span>
                            @endif
                        </li>

                        <li class="gr-2 float-right actions">
                            @if (Auth::isAdmin())
                                <div>
                                    <a class="link tooltip" href="/missions/{{ $mission->slug }}/edit" data-tooltip="Edit Mission">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            @endif
                            <div>
                                <a href="https://twitter.com/spacexstats" class="tooltip" data-tooltip="@spacexstats on Twitter">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </div>
                            <div>
                                @if (Auth::isMember())
                                    <a class="link tooltip" href="/users/{{Auth::user()->username}}/edit#email-notifications" data-tooltip="Email Notifications">
                                        <i class="fa fa-envelope-o"></i>
                                    </a>
                                @else
                                    <a class="link tooltip" href="/docs#email-notifications" data-tooltip="Email Notifications">
                                        <i class="fa fa-envelope-o"></i>
                                    </a>
                                @endif
                            </div>
                            @if ($mission->isLaunchPrecise())
                                <div>
                                    <a class="link tooltip" href="/calendars/{{ $mission->slug }}" data-tooltip="Download Calendar">
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                </div>
                                <div>
                                    <a class="tooltip" href="http://www.google.com/calendar/render?cid={{ url() }}/calendars/{{ $mission->slug }}" data-tooltip="Add to Google Calendar">
                                        <i class="fa fa-google"></i>
                                    </a>
                                </div>
                            @endif
                            <div>
                                <a class="tooltip" href="" data-tooltip="RSS Feed">
                                    <i class="fa fa-rss"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <section class="highlights">
                    @if(isset($pastMission))
                        <a href="/missions/{{ $pastMission->slug }}">
                            <div class="mission-link past-mission-link">
                                <span class="placeholder">Previous Mission</span>
                                <span class="link"><i class="fa fa-arrow-left"></i> {{ $pastMission->name }}</span>
                            </div>
                        </a>
                    @endif
                    @if(isset($futureMission))
                        <a href="/missions/{{ $futureMission->slug }}">
                            <div class="mission-link future-mission-link">
                                <span class="link">{{ $futureMission->name }} <i class="fa fa-arrow-right"></i></span>
                                <span class="placeholder">Next Mission</span>
                            </div>
                        </a>
                    @endif

                    <div class="webcast-status" ng-class="webcast.status" ng-if="isLaunchExact" ng-show="webcast.status != 'webcast-inactive'">
                        <span>@{{ webcast.publicStatus }}</span><span class="live-viewers" ng-show="webcast.status === 'webcast-live'">@{{ webcast.publicViewers }}</span>
                    </div>

                    <launch-date launch-specificity="launchSpecificity" launch-date-time="launchDateTime"></launch-date>
                </section>

                <section class="hero scrollto" id="countdown">
                    <countdown specificity="launchSpecificity" is-paused="isLaunchPaused" countdown-to="launchDateTime" callback="requestFrequencyManager" type="classic"></countdown>
                </section>
                <p>{{ $mission->summary }}</p>

                <h2>Details</h2>
                <section class="scrollto" id="details">
					
                    @include('templates.cards.missionCard', ['size' => 'large', 'mission' => $mission])
					<h2>Vehicle Details</h2>
					<h3>{{ isset($mission->firstStage) ? $mission->firstStage->part->name : 'Unspecified'  }} First Stage</h3>
                    <h3>{{ isset($mission->upperStage) ? $mission->upperStage->part->name : 'Unspecified'  }} Upper Stage</h3>
                   

                    @if ($mission->payloads->count() > 0)
                        <h3>Satellites To Be Launched</h3>
                        @include('templates.cards.payloadsCard', ['mission' => $mission])
                    @endif
                </section>

                <h2>Images</h2>
                <section id="images" class="scrollto">
                    @if ($images->count() > 0)
                        @foreach ($images as $image)
                            <div class="square">
                                <a href="/missioncontrol/objects/{{ $image->object_id }}">
                                    <img src="{{ $image->media_thumb_small }}" alt="{{ $image->summary }}" class="square" />
                                </a>
                            </div>
                        @endforeach
                        @if ($images->count() > 20)
                            <div class="square">
                                <p class="exclaim">{{ $images->count() - 20 }} more...</p>
                            </div>
                        @endif
                    @else
                        @if (Auth::isSubscriber())
                            <p class="exclaim">No images</p>
                        @else
                            <p class="exclaim">No public images</p>
                        @endif
                    @endif
                </section>

                <h2>Timeline</h2>
                <section class="scrollto" id="timeline">
                    <h3>Prelaunch</h3>
					@if ($mission->prelaunchEvents->count() > 0)
						<timeline mission="mission"></timeline>
					@else
						<p class="exclaim">No Prelaunch Events</p>
					@endif
                </section>

                <h2>Articles</h2>
                <section class="scrollto" id="articles">
                    @if ($mission->articles->count() == 0)
                        <p class="exclaim">No articles yet.</p>
                    @endif
                    @foreach ($mission->articles() as $article)
                    @endforeach
                </section>
            </main>
        </div>
    </body>
@stop