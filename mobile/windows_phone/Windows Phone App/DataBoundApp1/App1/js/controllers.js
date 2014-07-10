var signinCtrl = angular.module('signinCtrl', []);

signinCtrl.controller('signinCtrl', ['$scope', '$http', function($scope, $http) {
    $http.get('data/signIn.json').success(function(data) {
	$scope.langs = data;
    });

    $scope.signIn = function (login, password) {
	console.log(login+"|"+password);
//	API.authenticate(login, password);
	location.assign('#/list');
    }

}]);

var listCtrl = angular.module('listCtrl', ['ngSanitize']);

listCtrl.controller('listCtrl', ['$scope', '$routeParams',function($scope, $routeParams) {

}]);
