@extends('templates.main')
@section('title', 'Editing Mission')

@section('content')
<body class="edit-mission" ng-app="missionApp" ng-controller="missionController" ng-strict-di>

    @include('templates.header')

    <div class="content-wrapper">
        <h1>Editing Mission @{{mission.data.name}}</h1>
        <main>
            <form name="editMissionForm" novalidate>
                <fieldset>
                    <legend>@{{ mission.data.name }} Mission</legend>

                    <ul>
                        <li class="gr-12">
                            <label>Mission Name</label>
                            <input type="text" name="mission-name" ng-model="mission.data.name" placeholder="Enter a unique mission name here" required />
                        </li>

                        <li class="gr-6">
                            <label>Contractor</label>
                            <input type="text" ng-model="mission.data.contractor" required/>
                        </li>

                        <li class="gr-6">
                            <label>Mission Type <i class="fa fa-info-circle"></i></label>
                            <span>Selecting the type of mission determines the mission icon and image, if it is not set.</span>
                            <select ng-model="mission.data.mission_type_id" ng-options="missionType.mission_type_id as missionType.name for missionType in data.missionTypes" required></select>
                        </li>

                        <li class="gr-8">
                            <label>Launch Date Time (UTC)</label>
                            <input type="text" ng-model="mission.data.launch_date_time" placeholder="Entering a text string is okay, but if a precise date is needed, please follow MySQL date format" required/>
                        </li>
						<li class="gr-4">
                            <label>Launch Specificity</label>
                            <select ng-model="mission.data.launch_specificity"  ng-options="launchSpecificity.value as launchSpecificity.text for launchSpecificity in data.specificities" required/>
                        </li>
						
                        <li class="gr-4">
                            <label>Vehicle</label>
                            <select ng-model="mission.data.vehicle_id" ng-options="vehicle.vehicle_id as vehicle.vehicle for vehicle in data.vehicles" required></select>

                        </li>

                        <li class="gr-4">
                            <label for="">Launch Site</label>
                            <select ng-model="mission.data.launch_site_id" ng-options="launchSite.location_id as launchSite.fullLocation for launchSite in data.launchSites" required></select>

                        </li>

                        <li class="gr-4">
                            <label for="">Destination</label>
                            <select ng-model="mission.data.destination_id" ng-options="destination.destination_id as destination.destination for destination in data.destinations" required></select>

                        </li>

                        <li class="gr-12">
                            <label for="">Summary</label>
                            <textarea ng-model="mission.data.summary" placeholder="Short mission summary goes here. Please keep it less than 500 characters." required maxlength="500"></textarea>
                        </li>

                        <li class="gr-4">
                            <label for="">Launch Illumination</label>
                            <select ng-model="mission.data.launch_illumination" ng-options="launchIllumination for launchIllumination in data.launchIlluminations">
                                <option value="">Unknown</option>
                            </select>
                        </li>

                        <li class="gr-4">
                            <label for="">Status</label>
                            <select ng-model="mission.data.status" ng-options="status for status in data.statuses"></select>
                        </li>

                        <li class="gr-4">
                            <label for="">Outcome</label>
                            <select ng-model="mission.data.outcome" ng-options="outcome for outcome in data.outcomes">
                                <option value="">Unknown</option>
                            </select>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend>Article</legend>
                    <ul>
                        <li class="gr-12">
                            <label for="">Article</label>
                            <textarea ng-model="mission.data.article" placeholder="Mission article" maxlength="5000" placeholder="You can use markdown here." ng-minlength="1000" character-count></textarea>
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend>Media</legend>
                    <ul>
                        <li class="gr-4">
                            <label>Launch Video</label>
                            <dropdown options="data.launchVideos" ng-model="mission.launch_video" unique-key="object_id" title-key="title" searchable="true"></dropdown>
                        </li>
                        <li class="gr-4">
                            <label for="">Mission Patch</label>
                            <dropdown options="data.missionPatches" ng-model="mission.mission_patch" unique-key="object_id" title-key="title" searchable="true"></dropdown>
                        </li>
                        <li class="gr-4">
                            <label for="">Press Kit</label>
                            <dropdown options="data.pressKits" ng-model="mission.press_kit" unique-key="object_id" title-key="title" searchable="true"></dropdown>
                        </li>
                        <li class="gr-4">
                            <label>Cargo Manifest</label>
                            <dropdown options="data.cargoManifests" ng-model="mission.cargo_manifest" unique-key="object_id" title-key="title" searchable="true"></dropdown>
                        </li>
                        <li class="gr-4">
                            <label>Prelaunch Press Conference</label>
                            <dropdown options="data.pressConferences" ng-model="mission.prelaunch_press_conference" unique-key="object_id" title-key="title" searchable="true"></dropdown>
                        </li>
                        <li class="gr-4">
                            <label>Postlaunch Press Conference</label>
                            <dropdown options="data.pressConferences" ng-model="mission.postlaunch_press_conference" unique-key="object_id" title-key="title" searchable="true"></dropdown>
                        </li>
                        <li class="gr-4">
                            <label>Featured Image</label>
                            <dropdown options="data.featuredImages" ng-model="mission.featured_image" unique-key="object_id" title-key="title" searchable="true" id-only="true"></dropdown>
                        </li>
                        <li class="gr-4">
                            <label>Reddit Discussion</label>
                            <input type="text" ng-model="mission.reddit_discussion" />
                        </li>
                        <li class="gr-4">
                            <label>Flight Club</label>
                            <input type="text" ng-model="mission.flight_club" />
                        </li>
                    </ul>
                </fieldset>

                <fieldset>
                    <legend>Parts</legend>
                    <div class="add-parts">
                        <button class="icon-button" ng-click="filters.parts.type = 'Booster'">Add a Booster</button>
                        <button class="icon-button" ng-click="filters.parts.type = 'First Stage'">Add a First Stage</button>
                        <button class="icon-button" ng-click="filters.parts.type = 'Upper Stage'">Add an Upper Stage</button>

                        <div ng-show="filters.parts.type !== ''">
                            <div ng-repeat="part in data.parts | filter:filters.parts">
                                <span>@{{ part.name }}</span>
                                <button ng-click="mission.addPartFlight(filters.parts.type, part)">Reuse This @{{ filters.parts.type }}</button>
                            </div>

                            <button ng-click="mission.addPartFlight(filters.parts.type)">Create A @{{ filters.parts.type }}</button>
                        </div>
                    </div>

                    <div ng-repeat="partFlight in mission.part_flights">
                        <h3>@{{ partFlight.part.name }}</h3>

                        <label>Name</label>
                        <input type="text" ng-model="partFlight.part.name" />

                        <div ng-if="partFlight.part.type == 'Booster' || partFlight.part.type == 'First Stage'">
                            <label>Landing Legs?</label>
                            <input type="checkbox" ng-model="partFlight.firststage_landing_legs" />

                            <label>Grid Fins?</label>
                            <input type="checkbox" ng-model="partFlight.firststage_grid_fins" />

                            <label>Engine</label>
                            <select ng-model="partFlight.firststage_engine" ng-options="firstStageEngine for firstStageEngine in data.firstStageEngines"></select>

                            <label>Engine Failures</label>
                            <input type="text" ng-model="partFlight.firststage_engine_failures" />

                            <label>MECO time (seconds)</label>
                            <input type="text" ng-model="partFlight.firststage_meco" />

                            <label>Landing Coords (lat)</label>
                            <input type="text" ng-model="partFlight.firststage_landing_coords_lat" />

                            <label>Landing Coords (lng)</label>
                            <input type="text" ng-model="partFlight.firststage_landing_coords_lng" />

                            <label>Baseplate Color</label>
                            <input type="text" ng-model="partFlight.baseplate_color" />
                        </div>


                        <div ng-if="partFlight.part.type == 'Upper Stage'">
                            <label>Engine</label>
                            <select ng-model="partFlight.upperstage_engine" ng-options="upperstageEngine for upperstageEngine in data.upperStageEngines"></select>

                            <label>Status</label>
                            <select ng-model="partFlight.upperstage_status" ng-options="upperstageStatus for upperstageStatus in data.upperStageStatuses">
                                <option value=""></option>
                            </select>

                            <label>SECO time (seconds)</label>
                            <input type="text" ng-model="partFlight.upperstage_seco"/>

                            <label>Decay Date</label>
                            <datetime ng-model="partFlight.decay_date" type="date" start-year="2002" is-null="true" nullable-toggle="true"></datetime>

                            <label>NORAD ID</label>
                            <input type="text" ng-model="partFlight.upperstage_norad_id" />

                            <label>International Designator</label>
                            <input type="text" ng-model="partFlight.upperstage_intl_designator" />
                        </div>

                        <label>Landed?</label>
                        <input type="checkbox" value="true" ng-model="partFlight.landed"/>

                        <label>Notes</label>
                        <textarea ng-model="partFlight.note"></textarea>

                        <button ng-click="mission.removePartFlight(part)">Remove this part</button>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Payloads</legend>
                    <button ng-click="mission.addPayload()">Add Payload</button>

                    <div ng-repeat="payload in mission.payloads" ng-form="@{{ 'payloadForm' + $index }}">
                        <ul>
                            <li class="gr-6">
                                <label>Payload Name</label>
                                <input type="text" ng-model="payload.name" required />
                            </li>
                            <li class="gr-6">
                                <label>Operator</label>
                                <input type="text" ng-model="payload.operator" required />
                            </li>
                            <li class="gr-4">
                                <label>Mass (KG)</label>
                                <input type="text" ng-model="payload.mass" pattern="[0-9$,.]*" />
                            </li>
                            <li class="gr-4">
                                <label>Is Payload Primary?</label>
                                <input type="checkbox" ng-model="payload.primary" />
                            </li>
                            <li class="gr-4">
                                <label>Gunter's Space Page Link</label>
                                <input type="text" ng-model="payload.link" />
                            </li>
                        </ul>
                        <button ng-click="mission.removePayload(payload)">Remove This Payload</button>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Spacecraft</legend>

                    <div class="add-spacecraft" ng-if="mission.spacecraft_flight == null">
                        <div ng-repeat="spacecraft in data.spacecraft">
                            <span>@{{ spacecraft.name }}</span>
                            <button ng-click="mission.addSpacecraftFlight(spacecraft)" ng-disabled="mission.spacecraft_flight != null">Reuse This Spacecraft</button>
                        </div>

                        <button ng-click="mission.addSpacecraftFlight()" ng-disabled="mission.spacecraft_flight != null">Add A Spacecraft</button>
                    </div>

                    <div ng-if="mission.spacecraft_flight != null">
                        <h3>@{{ mission.spacecraft_flight.spacecraft.name }}</h3>

                        <label>Name</label>
                        <input type="text" ng-model="mission.spacecraft_flight.spacecraft.name" />

                        <label>Type</label>
                        <select ng-model="mission.spacecraft_flight.spacecraft.type" ng-options="spacecraftType for spacecraftType in data.spacecraftTypes"></select>

                        <label>Flight Name</label>
                        <input type="text" ng-model="mission.spacecraft_flight.flight_name" />

                        <label>End Of Mission</label>
                        <datetime type="datetime" ng-model="mission.spacecraft_flight.end_of_mission" is-null="true" nullable-toggle="true" start-year="2010"></datetime>

                        <label>Return Method</label>
                        <select ng-model="mission.spacecraft_flight.return_method" ng-options="returnMethod for returnMethod in data.returnMethods"></select>

                        <label>Upmass</label>
                        <input type="text" ng-model="mission.spacecraft_flight.upmass" />

                        <label>Downmass</label>
                        <input type="text" ng-model="mission.spacecraft_flight.downmass" />

                        <label>ISS Berth</label>
                        <datetime type="datetime" ng-model="mission.spacecraft_flight.iss_berth" is-null="true" nullable-toggle="true" start-year="2010"></datetime>

                        <label>ISS Unberth</label>
                        <datetime type="datetime" ng-model="mission.spacecraft_flight.iss_berth" is-null="true" nullable-toggle="true" start-year="2010"></datetime>

                        <fieldset>
                            <label>Astronauts</label>

                            <select ng-model="selected.astronaut" ng-options="astronaut as astronaut.fullName for astronaut in data.astronauts">
                                <option value="">New...</option>
                            </select>
                            <button ng-click="mission.spacecraft_flight.addAstronautFlight(selected.astronaut)">Add Astronaut</button>

                            <div ng-repeat="astronautFlight in mission.spacecraft_flight.astronaut_flights">
                                <h3>@{{ astronautFlight.astronaut.full_name }}</h3>
                                <label>First Name</label>
                                <input type="text" ng-model="astronautFlight.astronaut.first_name" />

                                <label>Last Name</label>
                                <input type="text" ng-model="astronautFlight.astronaut.last_name" />

                                <label>Gender</label>
                                <input type="radio" name="gender" value="Male" ng-model="astronautFlight.astronaut.gender" />Male
                                <input type="radio" name="gender" value="Female" ng-model="astronautFlight.astronaut.gender" />Female

                                <label>Deceased</label>
                                <input type="checkbox" ng-model="astronautFlight.astronaut.deceased"  />

                                <label>Date of Birth</label>
                                <datetime type="date" ng-model="astronautFlight.astronaut.date_of_birth" is-null="true" nullable-toggle="true" start-year="2010"></datetime>

                                <label>Nationality</label>
                                <input type="text" ng-model="astronautFlight.astronaut.nationality" />

                                <button ng-click="mission.spacecraft_flight.removeAstronautFlight(astronautFlight)">Remove Astronaut</button>
                            </div>
                        </fieldset>

                        <button ng-click="mission.removeSpacecraftFlight()">Remove Spacecraft</button>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Prelaunch Events</legend>
                    <span>Launch announcements and datetime changes are added automatically.</span>

                    <div ng-repeat="prelaunchEvent in mission.prelaunch_events">
                        @{{ prelaunchEvent.eventType }}
                    </div>

                    <!-- Prelaunch event stuff here -->
                    <button ng-click="mission.addPrelaunchEvent()">Add Prelaunch Event</button>

                </fieldset>

                <fieldset>
                    <legend>Telemetry</legend>
                    <span>Add readouts and live statistics of the launch here.</span>

                    <table ng-if="mission.telemetry.length > 0">
                        <tr>
                            <th>Timestamp (T+ s)</th>
                            <th>Readout</th>
                            <th>Altitude (m)</th>
                            <th>Velocity (m/s)</th>
                            <th>Downrange (m)</th>
                            <th></th>
                        </tr>
                        <tr ng-repeat="telemetry in mission.telemetry">
                            <td><input type="number" ng-model="telemetry.timestamp" required /></td>
                            <td><input type="text" ng-model="telemetry.readout" /></td>
                            <td><input type="number" ng-model="telemetry.altitude" /></td>
                            <td><input type="number" ng-model="telemetry.velocity" /></td>
                            <td><input type="number" ng-model="telemetry.downrange" /></td>
                            <td><button ng-click="mission.removeTelemetry(telemetry)">Remove This Readout</button></td>
                        </tr>
                    </table>

                    <button ng-click="mission.addTelemetry()">Add Readout</button>

                </fieldset>

                <input type="submit" ng-click="mission.make()" ng-disabled="editMissionForm.$invalid" value="Save Mission"/>
            </form>
        </main>
    </div>

    <script type="text/javascript">
        (function() {
            var app = angular.module("app");
            app.constant("CSRF_TOKEN", '{{ csrf_token() }}');
        })();
    </script>
</body>
@stop

