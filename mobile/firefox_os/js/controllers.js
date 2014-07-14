//PROPERTIES MODULE


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


//LOGIN MODULE

var signinCtrl = angular.module('signinCtrl', ['ionic']);

signinCtrl.controller('signinCtrl',
		      ['$scope', '$http', '$location', '$ionicPopup',
		       function($scope, $http, $location, $ionicPopup) {
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
		errorMessage = "Login or Password invalid";
	    else if (data.error.search("invalid_request") != -1)
		errorMessage = "Login or Password required";
	    else
		errorMessage = data.error;
	    console.log(data);   

	    var alertPopup = $ionicPopup.alert({
		title: 'ERROR',
		template: errorMessage,
	    });
	});
	//	API.authenticate(login, password);
	//	location.assign('#/list');
    }

}]);


//LIST MODULE

var listCtrl = angular.module('listCtrl', ['ngSanitize', 'ionic']);

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


listCtrl.controller('listCtrl', ['$scope', '$routeParams', '$http', '$window', '$location', '$ionicModal',function($scope, $routeParams, $http, $window, $location, $ionicModal) {
    $scope.loading = true;
    justLongPressed = false;
    console.log(MimeType.init());
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
	
	data.files.forEach(function(file) {
	    file.info["mime_type"] = MimeType.lookup(file.info.path);
	    file.info["icon"] = getImg(file.info.mime_type);
	});

	$scope.folders = data.folders;
	$scope.files = data.files;
	console.log($scope.folders);
	console.log($scope.files);
	
    }).error(function(data) {
	$scope.loading = false;
    	console.log(data);
    });

    $ionicModal.fromTemplateUrl('my-modal.html', {
	scope: $scope,
	animation: 'slide-in-up'
    }).then(function(modal) {
	$scope.modal = modal;
    });

    var fileToUrl = function(pseudo_owner, path, real_path) {
	var url = localStorage.getItem("server.url")+'uploads/' + pseudo_owner + '/' + real_path + path;
	return url;
    }
    var getImg = function(mime_type) {
	if (mime_type.search("audio") != -1)
	    img = "img/icone/ios7-musical-notes.png";
	else if (mime_type.search("video") != -1)
	    img = "img/icone/ios7-film.png";
	else if (mime_type.search("image") != -1)
	    img = "img/icone/image.png";
	else if (type.search("epub") != -1
		 ||mime_type.search("ebook") != -1)
	    img = "img/icone/android-book.png";
	else if (mime_type.search("text") != -1
		 || mime_type.search("msword") != -1
		 || mime_type.search("pdf") != -1)
	    img = "img/icone/document-text.png";
	else if (mime_type.search("zip") != -1
		 || mime_type.search("tar")!= -1)
	    img = "img/icone/ios7-box.png";
	else
	    img = "img/icone/ios7-help-empty.png";
	return img;
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

    $scope.itemOnTouchEnd = function(pseudo_owner, path, real_path, name) {
	if (justLongPressed == false) {
	    path = fileToUrl(pseudo_owner, path, real_path);
	    console.log(path);
	    MimeType.init();
	    if (MimeType.lookup(path).search("audio") != -1)
		url = "/musicPlayer/"+encodeURIComponent(path)+"/"+name;
	    else if (MimeType.lookup(path).search("video") != -1)
		url = "/videoPlayer/"+encodeURIComponent(path)+"/"+name;
	    else if (MimeType.lookup(path).search("image") != -1)
		url = "/imagePlayer/"+encodeURIComponent(path)+"/"+name;
	    console.log(url);
	    $location.path(url);
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
	$scope.modal.show();
	setTimeout(function(){
            justLongPressed = false;
        }, 500);

    }

    $scope.closeModal = function(fileId) {
	$scope.modal.hide();
    }

    $scope.download = function(fileId) {
	console.log(fileId);
/*	$http({
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
    */}
}]);

//MUSIC PLAYER MODULE

var musicPlayerCtrl = angular.module('musicPlayerCtrl', ['ngSanitize', 'mediaPlayer']);

musicPlayerCtrl.controller('musicPlayerCtrl', ['$scope', '$routeParams', '$sce', function($scope, $routeParams, $sce) {
//    console.log($scope.audio1.network);
//    console.log($scope.audio1.ended);
//    $scope.audio1.play();
    MimeType.init();
    $scope.mimetype =  MimeType.lookup(decodeURIComponent($routeParams.url));
    $scope.srcUrl = $sce.trustAsResourceUrl(decodeURIComponent($routeParams.url));
    $scope.title = $routeParams.name;
//    $scope.audio1.loading(true);
    setTimeout(function(){
	var angularmp = angular.element(document.querySelector('audio')).scope().mediaPlayer;
	var angularpl = angular.element(document.querySelector('audio')).scope().mediaPlaylist;
	angularmp.load(true);
	angularpl.push({ src: $scope.srcUrl, type: $scope.mimetype});
    }, 1000);

    $scope.getUrl = function() {
	url = $sce.trustAsResourceUrl(decodeURIComponent($routeParams.url));
	console.log(url);
	return url;
    }

    $scope.getType = function() {
	type =  MimeType.lookup(decodeURIComponent($routeParams.url));
	console.log(type);
	return type;
    }

    $scope.seekPercentage = function ($event) {
	var percentage = ($event.offsetX / $event.target.offsetWidth);
	if (percentage <= 1) {
	    return percentage;
	} else {
	    return 0;
	}
    }
}]);

var videoPlayerCtrl = angular.module('videoPlayerCtrl', ['ngSanitize']);

videoPlayerCtrl.controller('videoPlayerCtrl', ['$scope', '$routeParams', '$sce', function($scope, $routeParams, $sce) {
    MimeType.init();
    $scope.mimetype = MimeType.lookup(decodeURIComponent($routeParams.url));
    $scope.srcUrl = $sce.trustAsResourceUrl(decodeURIComponent($routeParams.url));
    $scope.title = $routeParams.name;
    console.log($scope.mimetype);
    $scope.videoHeight = window.innerHeight - 43;
    $scope.videoWidth = window.innerWidth;
    console.log($scope.videoHeight);
    console.log($scope.videoWidth);
}]);

var imagePlayerCtrl = angular.module('imagePlayerCtrl', ['ngSanitize', 'ui.bootstrap']);

imagePlayerCtrl.controller('imagePlayerCtrl', ['$scope', '$routeParams', '$sce', function($scope, $routeParams, $sce) {
    MimeType.init();
    $scope.mimetype = MimeType.lookup(decodeURIComponent($routeParams.url));
    $scope.srcUrl = $sce.trustAsResourceUrl(decodeURIComponent($routeParams.url));
    $scope.title = $routeParams.name;
    console.log($scope.mimetype);
    $scope.videoHeight = window.innerHeight - 43;
    $scope.videoWidth = window.innerWidth;
    console.log($scope.videoHeight);
    console.log($scope.videoWidth);
}]);
