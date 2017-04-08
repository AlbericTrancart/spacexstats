<div class="card mission-card {{ $size }}">
	<div class="top">
		<div class="thumb" style="background-image:url('{{ $mission->present()->featuredImageUrl() }}');"></div>
		<p>
            <a class="mission-name" href="/missions/{{ $mission->slug }}">{{ $mission->name }}</a>
			<span class="for hide@small"> for </span>
			<span class="mission-contractor hide@small">{{ $mission->contractor }}</span>
        </p>
	</div>
	<div class="bottom">
		<div class="container values">
			<div>
				<span class="launch-count">{{ ordinal($mission->generic_vehicle_count) }}</span>
			</div>
			<div>{{ $mission->present()->launchDateTime() }}</div>
			<div>{{ ordinal($mission->launch_of_year) }}</div>
			<div>{{ $mission->vehicle->specific_vehicle }}</div>
			<div>{{ $mission->destination->destination }}</div>
			<div>{{ $mission->launchSite->full_location }}</div>
			
			@if ($mission->status == 'Complete')
                @if ($mission->outcome == 'Success')
                    <div>{{ ordinal($mission->successful_consecutive_launch) }}</div>
                @endif
				<div><img class="launch-illumination" src="/images/icons/illuminations/{{ $mission->launch_illumination }}.png" /></div>
			@endif
			<div>
                <a href="/missioncontrol/collections/mission/{{ $mission->slug }}">
                    <img class="mission-collection" src="/images/icons/collection.png" alt="{{ $mission->name }} mission collection"/>
                </a>
            </div>
		</div>
		<div class="container keys">
			<div>{{ $mission->vehicle->generic_vehicle }}<br/>Launch</div>
			<div>Launch (UTC)</div>
			<div>Launch of the Year</div>
			<div>Vehicle</div>
			<div>Destination</div>
			<div>Launch Site</div>
			@if ($mission->status == 'Upcoming')
                <div>Probability of Launch</div>
				<!--<div>Where to watch</div>-->
			@endif
			@if ($mission->status == 'Complete')
                @if ($mission->outcome == 'Success')
                    <div>Successful Consecutive Launch</div>
                @endif
				<div>{{ $mission->launch_illumination }} Launch</div>
			@endif
			<div><a href="/missioncontrol/collections/mission/{{ $mission->slug }}">Mission Collection</a></div>
		</div>
		<p><em>{{ $mission->summary }}</em></p>
	</div>
</div>