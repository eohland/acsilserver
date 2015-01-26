/*global angular:true*/
angular.module('acsilUser', ['ngResource']).factory('User', [
	'$resource', function($resource) {
		'use strict';
		return $resource('../service/index.php/User/:id', {}, {
			'create': {method: 'PUT'},
			'update': {method: 'POST'}
		});
	}
]);
