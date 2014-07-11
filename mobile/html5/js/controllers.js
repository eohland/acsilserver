var signinCtrl = angular.module('signinCtrl', []);

signinCtrl.controller('signinCtrl', ['$scope', '$http', '$location', function($scope, $http, $location) {
    $scope.signIn = true;
    $scope.loading = false;
    $scope.error = false;
    
    $http.get('data/signIn.json').success(function(data) {
	$scope.langs = data;
    });

    $scope.signIn = function (login, password) {
	console.log(login+"|"+password);
	$scope.loading = true;
	myData = $.param({
	    grant_type: "password",
	    username: login,
	    password: password,
	    client_id: "1_1czy7ecwsklcw84c8woococ4cg0ko44cwoosgkgw8w0kcck448",//*/"3_1zm54gls83c0ko4gwk8cg44wsgskkckssg80occ8ssw0ww0wwk",
	    client_secret: "2k4nxulmjk2swsws00ooosswoo40ko0sok04c8kss4sk4woo0g"//*/"27yd9lyhqj40wkwccgok8848woo80c00ksgocck08s8k80cgwc"
	});
	console.log(myData);
	$http({
	    method: 'POST',
	    url: localStorage.getItem("server.url")+"app_dev.php/oauth/v2/token",
	    data: myData,
	    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	}).success(function(data) {
	    console.log(data);
	    localStorage.setItem("credential.password", password);
	    localStorage.setItem("credential.username", login);
	    localStorage.setItem("credential.access_token", data.access_token);
	    localStorage.setItem("credential.refresh_token", data.refresh_token);
	    alert("tata");
	    setTimeout(function(){
		alert("toto");
		$scope.loading = false;
		$location.path('/list').replace();
		$scope.$apply();
	    }, 200);
	}).error(function(data) {
	    $scope.loading = false;
	    $scope.error = true;
	    if (data.error.search("invalid_grant") != -1)
		$scope.errorMessage = "Login or Password invalid";
	    else if (data.error.search("invalid_request") != -1)
		$scope.errorMessage = "Login or Password required";
	    else
		$scope.errorMessage = data.error/	    console.log(data);   
	});
	//	API.authenticate(login, password);
	//	location.assign('#/list');
    }

}]);

var listCtrl = angular.module('listCtrl', ['ngSanitize']);

listCtrl.run(function($http) {
    $http.defaults.headers.common.Authorization = 'Bearer '
	+ localStorage.getItem("credential.access_token");
});

listCtrl.controller('listCtrl', ['$scope', '$routeParams', '$http',function($scope, $routeParams, $http) {
    $scope.loading = true;
    url = localStorage.getItem("server.url");
    $http({
	method: 'POST',
	url: localStorage.getItem("server.url")+'app_dev.php/service/1/op/list',	
    }).success(function(data) {
	$scope.loading = false;
	console.log(data);
	$scope.folders = data.folders;
	$scope.files = data.files;
	
    }).error(function(data) {
	$scope.loading = false;
    	console.log(data);
    });

    $scope.fileToUrl = function(pseudo_owner, path) {
	var url = localStorage.getItem("server.url")+'uploads/' + pseudo_owner + '/' + path;
	return url;
    }
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
