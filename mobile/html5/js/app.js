var clientApp = angular.module('clientApp', [
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'ui.bootstrap',
    'signinCtrl',
    'propertiesDirective',
    'listCtrl',
//    'uploadCtrl',
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
	when('/list/:id', {
            templateUrl: 'partials/list.html',
            controller: 'listCtrl'
	}).
	when('/upload/:id', {
            templateUrl: 'partials/upload.html',
            controller: 'uploadCtrl'
	}).
	otherwise({
            redirectTo: '/list/0'
	});
}]);

clientApp.controller('AppCtrl', ['$scope', function($scope) {
    $scope.back = function() { 
	window.history.back();
    };

    $scope.showBack = function() {
	if (location.hash.search("signin") == -1 && location.hash.search("list/0") == -1)
	    return true;
	else
	    return false;
    }

    $scope.showUpload = function() {
	if (location.hash.search("signin") == -1 && location.hash.search("properties") == -1)
	    return true;
	else
	    return false;
    }

    $scope.getCurrentFolderId = function() {
	//var id = location.hash.substring(location.hash.lastIndexOf("/"), location.hash.lenght);
	//return id;
    }

}]);
