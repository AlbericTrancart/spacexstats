// Original jQuery countdown timer written by /u/EchoLogic, improved and optimized by /u/booOfBorg.
// Rewritten as an Angular directive for SpaceXStats 4
(function() {
    var app = angular.module('app');

    app.directive('launchDate', ['$interval', '$filter', function($interval, $filter) {
        return {
            restrict: 'E',
            scope: {
                launchSpecificity: '=',
                launchDateTime: '='
            },
            link: function($scope, elem, attrs) {
                /*
                 *   Timezone stuff.
                 */
                // Get the IANA Timezone identifier and format it into a 3 letter timezone.
                $scope.localTimezone = moment().tz(jstz.determine().name()).format('z');

                // Set the format to be displayed based on the launch specificity
                switch ($scope.launchSpecificity) {
					case "6":
                    case 6:
                        $scope.currentFormat = 'MMMM d, yyyy';
                        break;
					case "7":
                    case 7:
                        $scope.currentFormat = 'h:mm:ssa MMMM d, yyyy';
                        break;
                    default:
                        $scope.currentFormat = null;
                }

                $scope.currentTimezone = null;
                $scope.currentTimezoneFormatted = "Local ("+ $scope.localTimezone +")";

                $scope.setTimezone = function(timezoneToSet) {
                    if (timezoneToSet === 'local') {
                        $scope.currentTimezone = null;
                        $scope.currentTimezoneFormatted = "Local ("+ $scope.localTimezone +")";
                    } else if (timezoneToSet === 'ET') {
                        $scope.currentTimezone = moment().tz("America/New_York").format('z');
                        $scope.currentTimezoneFormatted = 'Eastern';
                    } else if (timezoneToSet === 'PT') {
                        $scope.currentTimezone = moment().tz("America/Los_Angeles").format('z');
                        $scope.currentTimezoneFormatted = 'Pacific';
                    } else {
                        $scope.currentTimezoneFormatted = $scope.currentTimezone = 'UTC';
                    }
                };

                $scope.isHoveringOverAlert = false;

                $scope.hoveringOverAlert = function() {
                    $scope.isHoveringOverAlert = !$scope.isHoveringOverAlert;
                };

                $scope.displayDateTime = function() {
                    //if ($scope.isHoveringOverAlert) {
                    //    return 'This launch has no time yet';
                    //}
                    if ($scope.launchSpecificity >= 6) {
                        return $filter('date')(moment.utc($scope.launchDateTime, 'YYYY-MM-DD HH:mm:ss').toDate(), $scope.currentFormat, $scope.currentTimezone);
                    } else {
                        return $scope.launchDateTime;
                    }

                };
            },
            templateUrl: '/js/templates/launchDate.html'
        }
    }]);
})();