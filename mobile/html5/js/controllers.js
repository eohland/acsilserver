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
	    setTimeout(function(){
		$scope.loading = false;
		$location.path('/list/0');
		$scope.$apply();
	    }, 2000);
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

listCtrl.directive('onLongPress', function($timeout) {
    return {
	restrict: 'A',
	link: function($scope, $elm, $attrs) {
	    $elm.bind('touchstart', function(evt) {
		// Locally scoped variable that will keep track of the long press
		$scope.longPress = true;
		
		// We'll set a timeout for 600 ms for a long press
		$timeout(function() {
		    if ($scope.longPress) {
			// If the touchend event hasn't fired,
			// apply the function given in on the element's on-long-press attribute
			$scope.$apply(function() {
			    $scope.$eval($attrs.onLongPress)
			});
		    }
		}, 600);
	    });
	    
	    $elm.bind('touchend', function(evt) {
		// Prevent the onLongPress event from firing
		$scope.longPress = false;
		// If there is an on-touch-end function attached to this element, apply it
		if ($attrs.onTouchEnd) {
		    $scope.$apply(function() {
			$scope.$eval($attrs.onTouchEnd)
		    });
		}
	    });
	}
    };
})

listCtrl.directive('onLongClick', function($timeout) {
    return {
	restrict: 'A',
	link: function($scope, $elm, $attrs) {
	    $elm.bind('mousedown', function(evt) {
		// Locally scoped variable that will keep track of the long press
		$scope.longClick = true;
		
		// We'll set a timeout for 600 ms for a long press
		$timeout(function() {
		    if ($scope.longClick) {
			// If the touchend event hasn't fired,
			// apply the function given in on the element's on-long-press attribute
			$scope.$apply(function() {
			    $scope.$eval($attrs.onLongClick)
			});
		    }
		}, 600);
	    });
	    
	    $elm.bind('mouseup', function(evt) {
		// Prevent the onLongPress event from firing
		$scope.longClick = false;
		// If there is an on-touch-end function attached to this element, apply it
		if ($attrs.onMouseUp) {
		    $scope.$apply(function() {
			$scope.$eval($attrs.onMouseUp)
		    });
		}
	    });
	}
    };
})


listCtrl.controller('listCtrl', ['$scope', '$routeParams', '$http', '$window', '$location',function($scope, $routeParams, $http, $window, $location) {
    $scope.loading = true;
    justLongPressed = false;
    //myUrl = localStorage.getItem("server.url");
    $http.defaults.headers.common.Authorization = 'Bearer '
	+ localStorage.getItem("credential.access_token");
    myData = $.param({folderId: $routeParams.id});

    $http({
	method: 'POST',
	url: localStorage.getItem("server.url")+'app_dev.php/service/1/op/list/' + $routeParams.id,
	data: myData,
	headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).success(function(data) {
	$scope.loading = false;
	console.log(data);
	$scope.folders = data.folders;
	$scope.files = data.files;
	
    }).error(function(data) {
	$scope.loading = false;
    	console.log(data);
    });

    var fileToUrl = function(pseudo_owner, path, real_path) {
	var url = localStorage.getItem("server.url")+'uploads/' + pseudo_owner + '/' + real_path + path;
	return url;
    }
    $scope.fileGetUrl = function(pseudo_owner, path, real_path) {
	var url = localStorage.getItem("server.url")+'uploads/' + pseudo_owner + '/' + real_path + path;
	return url;
    }

    $scope.timeToReadable = function(str) {
	newstr = str.slice(0, str.search("T"));
	return newstr;
    }

    $scope.byteToReadable = function(nbr) {
	if (nbr < 1000) {
	    return nbr + "B";
	}
	else if (nbr >= 1000 && nbr < 1000000) {
	    nbr = nbr / 1000;
	    return nbr + "kB";
	}
	else if (nbr >= 1000000) {
	    nbr = nbr / 1000000;
	    return nbr + "MB";
	}
	
    }

    $scope.itemOnTouchEnd = function(pseudo_owner, path, real_path) {
	if (justLongPressed == false) {
	    path = fileToUrl(pseudo_owner, path, real_path);
	    window.location.assign(path);
	    alert("patate");
	}
    }

    $scope.folderOnTouchEnd = function(id) {
	$location.path('/list/'+id);
    }

    $scope.itemOnLongPress = function(id) {
	//alert("toto");
        justLongPressed = true;
	$scope.showid = id;
//	$scope.$apply();
	setTimeout(function(){
            justLongPressed = false;
        }, 500);

    }

    $scope.download = function(fileId) {
	$http({
	    method: 'POST',
	    url: localStorage.getItem("server.url")+'app_dev.php/service/1/op/download/' + fileId,
	    data: myData,
	    headers: {
		'Content-Type': 'application/x-www-form-urlencoded'
		
	    }
	}).success(function(data) {
	    $scope.loading = false;
	    console.log(data);
	}).error(function(data) {
	    $scope.loading = false;
    	    console.log(data);
	});
	alert("prout");
    }

}]);

var propertiesCtrl = angular.module('propertiesDirective', ['ngSanitize']);



propertiesCtrl.controller('propertiesCtrl', ['$scope', '$http',function($scope, $http) {
    $scope.session = localStorage.getItem("credential.access_token");
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
