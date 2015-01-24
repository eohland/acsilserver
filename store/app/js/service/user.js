/*global angular:true*/
angular.module('acsilstore', ['ngResource']).factory('User', [
	'$resource', function($resource) {
		'use strict';
		return $resource('../service/index.php/User/:id', {}, {
			'create': {method: 'PUT'},
			'update': {method: 'POST'}
		});
	}
]);
