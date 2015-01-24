/*global angular:true*/
angular.module('acsilstore', ['ngResource']).factory('Module', [
	'$resource', function($resource) {
		'use strict';
		return $resource('../service/index.php/Plugin/:id', {}, {
			'create': {method: 'PUT'},
			'update': {method: 'POST'}
		});
	}
]);
