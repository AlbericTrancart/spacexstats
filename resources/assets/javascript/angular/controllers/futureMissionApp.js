(function() {
    var app = angular.module('app', []);

    app.controller("futureMissionController", ['$http', '$scope', '$filter', 'flashMessage', function($http, $scope, $filter, flashMessage) {
		$scope.mission = laravel.mission;
        $scope.missionSlug = laravel.mission.slug;
        $scope.launchSpecificity = laravel.mission.launch_specificity;
        $scope.isLaunchPaused = laravel.mission.launch_paused;

        if ($scope.launchSpecificity >= 6) {
            $scope.launchDateTime = moment.utc(laravel.mission.launch_date_time);
        } else {
            $scope.launchDateTime = laravel.mission.launch_date_time;
        }

        $scope.$watch("launchSpecificity", function(newValue) {
            $scope.isLaunchExact = newValue >= 6;
        });

        $scope.lastRequest = moment().utc();
        $scope.secondsSinceLastRequest = $scope.secondsToLaunch = 0;

        $scope.requestFrequencyManager = function() {
            $scope.secondsSinceLastRequest = moment.utc().diff($scope.lastRequest, 'second');
            $scope.secondsToLaunch = moment.utc().diff(moment.utc($scope.launchDateTime, 'YYYY-MM-DD HH:mm:ss'), 'second');

            /*
             Make requests to the server for launchdatetime and webcast updates at the following frequencies:
             >24hrs to launch    =   1hr / request
             1hr-24hrs           =   15min / request
             20min-1hr           =   5 min / request
             <20min              =   30sec / request
             */
            var aRequestNeedsToBeMade = ($scope.secondsToLaunch <= -86400 && $scope.secondsSinceLastRequest >= 3600) ||
                ($scope.secondsToLaunch <= -3600 && $scope.secondsToLaunch > -86400 && $scope.secondsSinceLastRequest >= 900) ||
                ($scope.secondsToLaunch <= -1200 && $scope.secondsToLaunch > -3600 && $scope.secondsSinceLastRequest >= 300) ||
                ($scope.secondsToLaunch > -1200 && $scope.secondsSinceLastRequest >= 30);

            if (aRequestNeedsToBeMade === true) {
                // Make both requests then update the time since last request
                $scope.requestLaunchDateTime();
                $scope.requestWebcastStatus();
                $scope.lastRequest = moment().utc();
            }
        };

        $scope.requestLaunchDateTime = function() {
            $http.get('/missions/' + $scope.missionSlug + '/launchdatetime')
                .then(function(response) {
                    // If there has been a change in the launch datetime, update
                        if (!$scope.launchDateTime.isSame(moment.utc(response.data.launchDateTime))) {

                            $scope.launchDateTime = moment.utc(response.data.launchDateTime);
                            $scope.launchSpecificity = response.data.launchSpecificity;
                            $scope.isLaunchPaused = response.data.launchPaused;
                            flashMessage.addOK('Launch time updated!');

                        }
                });
        };

        $scope.requestWebcastStatus = function() {
            $http.get('/webcast/getstatus')
                .then(function(response) {
                    $scope.webcast.isLive = response.data.isLive;
                    $scope.webcast.viewers = response.data.viewers;
                });
        };

        $scope.webcast = {
            isLive: laravel.webcast.isLive,
            viewers: laravel.webcast.viewers
        };

        $scope.$watchCollection('[webcast.isLive, secondsToLaunch]', function(newValues) {
            if (newValues[1] < (60 * 60 * 24) && newValues[0] == 'true') {
                $scope.webcast.status = 'webcast-live';
            } else if (newValues[1] < (60 * 60 * 24) && newValues[0] == 'false') {
                $scope.webcast.status = 'webcast-updates';
            } else {
                $scope.webcast.status = 'webcast-inactive';
            }
        });

        $scope.$watch('webcast.status', function(newValue) {
            if (newValue === 'webcast-live') {
                $scope.webcast.publicStatus = 'Live Webcast'
            } else if (newValue === 'webcast-updates') {
                $scope.webcast.publicStatus = 'Launch Updates'
            }
        });

        $scope.$watch('webcast.viewers', function(newValue) {
            $scope.webcast.publicViewers = ' (' + newValue + ' viewers)';
        });
    }]);
})();