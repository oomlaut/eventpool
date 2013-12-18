'use strict';

eventpool.controller('EventCtrl', function EventCtrl($scope, eventpoolStorage){
	$scope.title = "Hi Brian!";
	$scope.dates = {};

	window.setInterval(function(){
			eventpoolStorage.getEventList().success(function(data){
			$scope.data = data;
		});
	}, 1000);


	$scope.editing = null;
	$scope.editingValue = null;

	$scope.claim = function(key){
		$scope.editing = key;
	}

	$scope.update = function(key, value){

		eventpoolStorage.submitEvent({
			"date": key,
			"value": value
		}).success(function(data){
			// console.log(data);
			$scope.data = data;
		});

		$scope.reset();
	}

	$scope.reset = function(){
		$scope.editing = null;
	}
});
