// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'

var clientApp = angular.module('clientApp', [
    'ionic',
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'signinCtrl',
    'propertiesDirective',
    'listCtrl',
    'musicPlayerCtrl',
    'videoPlayerCtrl',
    'imagePlayerCtrl',
    "com.2fdevs.videogular",
    "com.2fdevs.videogular.plugins.controls",
    "com.2fdevs.videogular.plugins.overlayplay",
    "com.2fdevs.videogular.plugins.buffering",
    "com.2fdevs.videogular.plugins.poster",
    "angularFileUpload",
    'uploadCtrl',
]);

clientApp.config(['$routeProvider', '$httpProvider', function($routeProvider, $httpProvider) {

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
	when('/musicPlayer/:url/:name', {
            templateUrl: 'partials/musicPlayer.html',
            controller: 'musicPlayerCtrl'
	}).
	when('/videoPlayer/:url/:name', {
            templateUrl: 'partials/videoPlayer.html',
            controller: 'videoPlayerCtrl'
	}).
	when('/imagePlayer/:url/:name', {
            templateUrl: 'partials/imagePlayer.html',
            controller: 'imagePlayerCtrl'
	}).
	when('/upload', {
            templateUrl: 'partials/upload.html',
            controller: 'uploadCtrl'
	}).
	otherwise({
            redirectTo: '/list/0'
	});
}]);

clientApp.run(function($ionicPlatform) {
  $ionicPlatform.ready(function() {
    // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
    // for form inputs)
    if(window.cordova && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
    }
    if(window.StatusBar) {
      StatusBar.styleDefault();
    }
  });
})


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
	if (location.hash.search("list") != -1)
	    return true;
	else
	    return false;
    }

    $scope.getCurrentFolderId = function() {
	//var id = location.hash.substring(location.hash.lastIndexOf("/"), location.hash.lenght);
	//return id;
    }

}]);
