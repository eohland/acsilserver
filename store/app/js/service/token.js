/*global angular:true*/
angular.module('acsilToken', ['ngResource']).factory('Token', [
	'$resource', function($resource) {
		'use strict';
		return $resource('../service/index.php/Token/:token', {}, {
			'create': {method: 'PUT'},
		});
	}
]);
