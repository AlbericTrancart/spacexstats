(function() {
    var app = angular.module('app', []);

    app.controller("missionController", ['$scope', 'missionService', function($scope, missionService) {
		if(typeof(laravel.mission) != 'undefined' && typeof(laravel.mission.launch_specificity) != 'undefined'){
			laravel.mission.launch_specificity = parseInt(laravel.mission.launch_specificity);
		}
		$scope.showAddPart = false;
		$scope.partFlight = {};
        // Scope the possible form data info
        $scope.data = {
            parts: laravel.parts,
            spacecraft: laravel.spacecraft,
            destinations: laravel.destinations,
            missionTypes: laravel.missionTypes,
            launchSites: laravel.launchSites,
            landingSites: laravel.landingSites,
            vehicles: laravel.vehicles,
            astronauts: laravel.astronauts,

            launchVideos: laravel.launchVideos ? laravel.launchVideos : null,
            missionPatches: laravel.missionPatches ? laravel.missionPatches : null,
            pressKits: laravel.pressKits ? laravel.pressKits : null,
            cargoManifests: laravel.cargoManifests ? laravel.cargoManifests : null,
            pressConferences: laravel.pressConferences ? laravel.pressConferences : null,
            featuredImages: laravel.featuredImages ? laravel.featuredImages: null,

            firstStageEngines: ['Merlin 1A', 'Merlin 1B', 'Merlin 1C', 'Merlin 1D'],
            upperStageEngines: ['Kestrel', 'Merlin 1C-Vac', 'Merlin 1D-Vac'],
            upperStageStatuses: ['Did not reach orbit', 'Decayed', 'Deorbited', 'Earth Orbit', 'Solar Orbit'],
            spacecraftTypes: ['Dragon 1', 'Dragon 2'],
            returnMethods: ['Splashdown', 'Landing', 'Did Not Return'],
            eventTypes: ['Wet Dress Rehearsal', 'Static Fire'],
            launchIlluminations: ['Day', 'Night', 'Twilight'],
            statuses: ['Upcoming', 'Complete', 'In Progress'],
            outcomes: ['Failure', 'Success'],
			specificities: [
				{'value' : 0, 'text' : 'Year'},
				{'value' : 1, 'text' : 'Half'},
				{'value' : 2, 'text' : 'Subyear'},
				{'value' : 3, 'text' : 'Quarter'},
				{'value' : 4, 'text' : 'Month'},
				{'value' : 5, 'text' : 'Submonth'},
				{'value' : 6, 'text' : 'Day'},
				{'value' : 7, 'text' : 'Precise'}	
			]
        };

        $scope.filters = {
            parts: {
                type: ''
            }
        };

        $scope.mission = {
            data: laravel.mission,
            make: function() {
                missionService.make($scope.mission.data);
            }
        };
		
		$scope.addPartFlight = function(partFlight){
			if(typeof(this.mission.data.part_flights) == 'undefined'){
				this.mission.data.part_flights = [];
			}
			partFlight.part.type = this.filters.parts.type;
			this.mission.data.part_flights.push(partFlight);
			this.partFlight = {};
			this.showAddPart = false;
		}
		
		$scope.reusePart = function(part){
			$scope.partFlight = {};
			$scope.partFlight.part = part;
			$scope.showAddPart = true;
		}
    }]);

    app.service("missionService", ["$http", "CSRF_TOKEN", function($http, CSRF_TOKEN) {
        this.make = function(mission) {
            if (mission.mission_id == null) {
                return $http.post('/missions/create', {
                    mission: mission,
                    _token: CSRF_TOKEN
                }).then(function (response) {
                    window.location = '/missions/' + response.data;
                });
            } else {
                return $http.patch('/missions/' + mission.slug + '/edit', {
                    mission: mission,
                    _token: CSRF_TOKEN
                }).then(function(response){
					window.location = '/missions/' + response.data;
				});;
            }
        };
    }]);
})();