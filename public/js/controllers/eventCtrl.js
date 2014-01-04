'use strict';

eventpool.controller('EventCtrl', function EventCtrl($scope, eventpoolStorage){
	$scope.title = "Date My Baby";
	$scope.pos = "content selected";
	$scope.neg = "content";
	$scope.data = {};

	// window.setInterval(function(){
		eventpoolStorage.getEventList().success(function(data){
			console.log(data);
			$scope.data = data;
		});
	// }, 1000);


	$scope.editing = null;
	$scope.editingValue = null;

	$scope.claim = function(key){
		$scope.editing = key;
	};

	$scope.update = function(key, value){
		// todo: validate field value

		eventpoolStorage.submitEvent({
			"date": key,
			"value": value
		}).success(function(data){
			// console.log(data);
			$scope.data = data;
		});

		$scope.reset();
	};

	$scope.reset = function(){
		$scope.editing = null;
	};
});
