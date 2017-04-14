(function() {
    var orderMissionsListApp = angular.module('app', []);

    orderMissionsListApp.controller("orderMissionsListController", ['$scope', 'orderMissionService', function($scope, orderMissionService) {
        $scope.missions = laravel.missions;
		$scope.incrementRemaining = function(orderId){
			for(var i=0; i<this.missions.length; i++){
				if(this.missions[i].launch_order_id >= orderId){
					this.missions[i].launch_order_id ++;
				}
			}
		}
		
		$scope.saveChanges = function(){
			orderMissionService.updateOrder(this.missions);
		}
    }]);
	orderMissionsListApp.service("orderMissionService", ["$http", "CSRF_TOKEN", function($http, CSRF_TOKEN) {
        this.updateOrder = function(missions) {
			//console.log(missions);
			return $http.post('/missions/editMissionOrder', {
				missions: missions,
				_token: CSRF_TOKEN
			}).then(function (response) {
				console.log(response)
			});
        };
    }]);
})();