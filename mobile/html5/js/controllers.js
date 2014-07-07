var signinCtrl = angular.module('signinCtrl', []);

signinCtrl.controller('signinCtrl', ['$scope', '$http', function($scope, $http) {
    $scope.signIn = true;
    
    $http.get('data/signIn.json').success(function(data) {
	$scope.langs = data;
    });

    $scope.signIn = function (login, password) {
	console.log(login+"|"+password);
	myData = $.param({grant_type: "password",
			  username: login,
			  password: password,
			  client_id: "1_27mi5mierc008884gswkkcsosowco84s4c4k88swwkw84ccgs4",
			  client_secret: "57ac67c1x1wckk8s0sgsogs0os0s0g0k88k8k0co0g08kgw4o8"});
	console.log(myData);
	//myData = decodeURIComponent(myData);
	//console.log(myData);
	$http({
	    method: 'POST',
	    url: "http://localhost/acs/app_dev.php/oauth/v2/token",
	    data: myData,
	    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	}).success(function(data) {
	    console.log(data);
	}).error(function(data) {
	    console.log(data);
	});
	//	API.authenticate(login, password);
	//	location.assign('#/list');
    }

}]);

var listCtrl = angular.module('listCtrl', ['ngSanitize']);

listCtrl.controller('listCtrl', ['$scope', '$routeParams',function($scope, $routeParams) {

}]);

var propertiesCtrl = angular.module('propertiesDirective', ['ngSanitize']);



propertiesCtrl.controller('propertiesCtrl', ['$scope', '$http',function($scope, $http) {
    $scope.session = utils.readCookie("session");
    $scope.server = [];
    $scope.server.url = localStorage.getItem("server.url");
}]);

propertiesCtrl.directive('save', ['$http', function($http) {
    return {
	require: 'ngModel',
	link: function(scope, ele, attrs, ctrl) {
	    ctrl.$parsers.unshift(function(viewValue) {
		console.log("value = "+viewValue);
		localStorage.setItem("server.url", viewValue);
	    });
	}
    }
}]);
