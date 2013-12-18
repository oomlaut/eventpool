'use strict';

eventpool.controller('EventCtrl', function EventCtrl($scope, $http){
	$scope.title = "Howdy, Partner";
	$scope.dates = {};
	$http({method: "get", url: "/controller/?action=load"}).success(function(data){
		console.log(data);
		$scope.data = data;
	})

	$scope.editing = null;

	$scope.claim = function(key){
		console.log(key);
		$scope.editing = key;
	}

	$scope.update = function(key){
		console.log(key);
		$scope.reset();
	}

	$scope.reset = function(){
		$scope.editing = null;
	}
});
