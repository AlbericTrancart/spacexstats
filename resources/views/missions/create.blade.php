@extends('templates.main')
@section('title', 'Create A Mission')

@section('content')
<body class="create-mission" ng-app="missionApp" ng-controller="missionController" ng-strict-di>


    @include('templates.header')

    <div class="content-wrapper">
        <h1>Create A Mission</h1>
        <main>

            <form name="createMissionForm">
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
                            <label>Mission Type <i class="fa fa-info" title="Selecting the type of mission determines the mission icon and image, if it is not set."></i></label>
                            <select ng-model="mission.data.mission_type_id" ng-options="missionType.mission_type_id as missionType.name for missionType in data.missionTypes" required></select>
                        </li>

                        <li class="gr-8">
                            <label>Launch Date Time</label>
                            <input type="text" ng-model="mission.data.launch_date_time" placeholder="Entering a text string is okay, but if a precise date is needed, please follow MySQL date format" required/>
                        </li>
						<li class="gr-4">
                            <label>Launch Specificity</label>
                            <select ng-model="mission.data.launch_specificity"  ng-options="launchSpecificity.value as launchSpecificity.text for launchSpecificity in data.specificities" required/></select>
                        </li>
                        <li class="gr-4">
                            <label>Vehicle</label>
                            <select ng-model="mission.data.vehicle_id" ng-options="vehicle.vehicle_id as vehicle.vehicle for vehicle in data.vehicles" required></select>

                        </li>

                        <li class="gr-4">
                            <label>Launch Site</label>
                            <select ng-model="mission.data.launch_site_id" ng-options="launchSite.location_id as launchSite.fullLocation for launchSite in data.launchSites" required></select>

                        </li>

                        <li class="gr-4">
                            <label>Destination</label>
                            <select ng-model="mission.data.destination_id" ng-options="destination.destination_id as destination.destination for destination in data.destinations" required></select>

                        </li>

                        <li class="gr-12">
                            <label>Summary</label>
                            <textarea ng-model="mission.data.summary" placeholder="Short mission summary goes here. Please keep it less than 500 characters." required maxlength="500"></textarea>
                        </li>

                        <li class="gr-12">
                            <input type="submit" value="Create Mission" ng-click="mission.make()" />
                        </li>
                    </ul>
                </fieldset>
            </form>

            <form>
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
                            <label>Engine</label>
                            <select ng-model="partFlight.firststage_engine" ng-options="firstStageEngine for firstStageEngine in data.firstStageEngines"></select>
                        </div>


                        <div ng-if="partFlight.part.type == 'Upper Stage'">
                            <label>Engine</label>
                            <select ng-model="partFlight.upperstage_engine" ng-options="upperStageEngine for upperStageEngine in data.upperStageEngines"></select>
                        </div>

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

                        <label>Return Method</label>
                        <select ng-model="mission.spacecraft_flight.return_method" ng-options="returnMethod for returnMethod in data.returnMethods"></select>

                        <fieldset>
                            <label>Astronauts</label>

                            <select ng-model="selected.astronaut" ng-options="astronaut as astronaut.fullName for astronaut in data.astronauts">
                                <option value="">New...</option>
                            </select>
                            <button ng-click="mission.spacecraftFlight.addAstronautFlight(selected.astronaut)">Add Astronaut</button>

                            <div ng-repeat="astronautFlight in mission.spacecraft_flight.astronaut_flights">
                                <h3>@{{ astronaut_flight.astronaut.full_name }}</h3>
                                <label>First Name</label>
                                <input type="text" ng-model="astronaut_flight.astronaut.first_name" />

                                <label>Last Name</label>
                                <input type="text" ng-model="astronaut_flight.astronaut.last_name" />

                                <label>Gender</label>
                                <input type="radio" name="gender" value="Male" ng-model="astronaut_flight.astronaut.gender" />Male
                                <input type="radio" name="gender" value="Female" ng-model="astronaut_flight.astronaut.gender" />Female

                                <label>Deceased</label>
                                <input type="checkbox" ng-model="astronaut_flight.astronaut.deceased"  />

                                <label>Date of Birth</label>

                                <label>Nationality</label>
                                <input type="text" ng-model="astronaut_flight.astronaut.nationality" />

                                <button ng-click="mission.spacecraft_flight.removeAstronautFlight(astronautFlight)">Remove Astronaut</button>
                            </div>
                        </fieldset>

                        <button ng-click="mission.removeSpacecraftFlight()">Remove Spacecraft</button>
                    </div>
                </fieldset>

                <input type="submit" ng-click="createMission(mission)" ng-disabled="createMissionForm.$invalid" value="Create Mission"/>
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