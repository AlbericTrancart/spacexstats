// Original jQuery countdown timer written by /u/EchoLogic, improved and optimized by /u/booOfBorg.
// Rewritten as an Angular directive for SpaceXStats 4
(function() {
    var app = angular.module('app');

    app.directive('countdown', ['$interval', function($interval) {
        return {
            restrict: 'E',
            scope: {
                countdownTo: '=',
                specificity: '=?',
                type: '@',
                isPaused: '=?',
                isVisibleWhenPaused: '=?',
                callback: '&?'
            },
            link: function($scope, elem, attrs) {

                if (attrs.callback) {
                    $scope.callback = $scope.callback();
                }

                $scope.isPaused = typeof $scope.isPaused !== 'undefined' ? $scope.isPaused : false;
				$scope.isPaused = $scope.isPaused == "0" ? false : $scope.isPaused;
                $scope.isVisibleWhenPaused = typeof $scope.isVisibleWhenPaused !== 'undefined' ? $scope.isVisibleWhenPaused : false;

                $scope.isLaunchExact = angular.isUndefined($scope.specificity) || $scope.specificity == 6 || $scope.specificity == 7;

                var splitSeconds = function(seconds) {
                    // Calculate the number of days, hours, minutes, seconds
                    $scope.days = Math.floor(seconds / (60 * 60 * 24));
                    seconds -= $scope.days * 60 * 60 * 24;

                    $scope.hours = Math.floor(seconds / (60 * 60));
                    seconds -= $scope.hours * 60 * 60;

                    $scope.minutes = Math.floor(seconds / 60);
                    seconds -= $scope.minutes * 60;

                    $scope.seconds = seconds;

                    $scope.daysText = $scope.days == 1 ? 'Day' : 'Days';
                    $scope.hoursText = $scope.hours == 1 ? 'Hour' : 'Hours';
                    $scope.minutesText = $scope.minutes == 1 ? 'Minute' : 'Minutes';
                    $scope.secondsText = $scope.seconds == 1 ? 'Second' : 'Seconds';
                };

                var countdownProcessor = function() {

                    if (!$scope.isPaused) {
                        var relativeSecondsBetween = moment.utc().diff(moment.utc($scope.countdownTo, 'YYYY-MM-DD HH:mm:ss'), 'second');
                        var secondsBetween = Math.abs(relativeSecondsBetween);

                        $scope.sign = relativeSecondsBetween <= 0 ? '-' : '+';
                        $scope.tMinusZero = secondsBetween == 0;

                        splitSeconds(secondsBetween);

                        if (attrs.callback) {
                            $scope.callback(relativeSecondsBetween);
                        }
                    }
                };

                // Countdown here
                if ($scope.isLaunchExact && $scope.type != 'interval') {
                    $interval(countdownProcessor, 1000);
                } else if ($scope.type == 'interval') {
                    splitSeconds($scope.countdownTo);
                } else {
                    $scope.countdownText = $scope.countdownTo;
                }
            },
            templateUrl: '/js/templates/countdown.html'
        }
    }]);
})();