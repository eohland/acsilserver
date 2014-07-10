var restaurantApp = angular.module('restaurantApp', [
    'ngRoute',
    'ui.bootstrap',
    'signinCtrl',
    'listCtrl'
]);

restaurantApp.config(['$routeProvider',function($routeProvider) {
    console.log($routeProvider);
    $routeProvider.
	when('/signin', {
            templateUrl: 'partials/signIn.html',
            controller: 'signinCtrl'
	}).
	when('/list', {
            templateUrl: 'partials/list.html',
            controller: 'listCtrl'
	}).
	otherwise({
            redirectTo: '/list'
	});
}]);
