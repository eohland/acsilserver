var clientApp = angular.module('clientApp', [
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'ui.bootstrap',
    'signinCtrl',
    'propertiesDirective',
    'listCtrl',
]);

clientApp.config(['$routeProvider', '$httpProvider', function($routeProvider, $httpProvider) {
    
    console.log($routeProvider);
/*    $httpProvider.defaults.useXDomain = true;
    $httpProvider.defaults.withCredentials = true;
    $httpProvider.defaults.useXDomain = true;
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
*/
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

    $scope.showBack = function() {
	console.log("hash="+location.hash+" search="+location.hash.search("list")); 
	console.log("hash="+location.hash+" search="+location.hash.search("signin")); 
	if (location.hash.search("signin") == -1 && location.hash.search("list") == -1)
	    return true;
	else
	    return false;
    }
}]);
