var clientApp = angular.module('clientApp', [
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'ui.bootstrap',
    'signinCtrl',
    'propertiesDirective',
    'listCtrl',
]);

clientApp.config(['$routeProvider', function($routeProvider, $locationProvider) {
    
    console.log($routeProvider);
    $routeProvider.
	when('/signin', {
            templateUrl: 'partials/signIn.html',
            controller: 'signinCtrl'
	}).
	when('/properties', {
            templateUrl: 'partials/properties.html',
            controller: 'propertiesCtrl'
	}).
	when('/list', {
            templateUrl: 'partials/list.html',
            controller: 'listCtrl'
	}).
	otherwise({
            redirectTo: '/list'
	});
}]);

clientApp.controller('AppCtrl', ['$scope', function($scope) {
    $scope.back = function() { 
	window.history.back();
    };

    $scope.signIn = function() {
	console.log("hash="+location.hash+" search="+location.hash.search("signin")); 
	if (location.hash.search("signin") == -1)
	    return false;
	else
	    return true;
    }
}]);
