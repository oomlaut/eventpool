'use strict';

$(document).foundation();

var eventpool = angular.module('eventpool', []);

eventpool.run(function($templateCache){
	$templateCache.put('available.html', 'Used when the date is unclaimed.');
	$templateCache.put('claimed.html', 'Used when the date is unavailable.');
});
