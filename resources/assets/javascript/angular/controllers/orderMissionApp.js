(function() {
    var orderMissionsListApp = angular.module('app', []);

    orderMissionsListApp.controller("orderMissionsListController", ['$scope', 'orderMissionService', function($scope, orderMissionService) {
        $scope.missions = laravel.missions;
		for(var i=0; i< $scope.missions.length; i++){
			if(typeof($scope.missions[i].launch_order_id) != 'undefined'){
				$scope.missions[i].launch_order_id = parseInt($scope.missions[i].launch_order_id);
			} 
		}
		$scope.incrementRemaining = function(missionId, orderId){
			for(var i=0; i<this.missions.length; i++){
				if(this.missions[i].launch_order_id >= orderId && this.missions[i].mission_id != missionId){
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