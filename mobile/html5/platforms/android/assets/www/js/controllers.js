//PROPERTIES MODULE


var propertiesCtrl = angular.module('propertiesDirective', ['ngSanitize']);

propertiesCtrl.controller('propertiesCtrl', ['$scope', '$http', '$location',function($scope, $http, $location) {
    $scope.session = localStorage.getItem("credential.access_token");
    $scope.server = [];
    $scope.server.url = localStorage.getItem("server.url");
    console.log("session="+ $scope.session);

    $scope.logout = function() {
    console.log("session="+ $scope.session);
	localStorage.removeItem("credential.password");
	localStorage.removeItem("credential.username");
	localStorage.removeItem("credential.access_token");
	localStorage.removeItem("credential.refresh_token");	
	$location.path('/signin');
    };
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
    };
}]);


//LOGIN MODULE

var signinCtrl = angular.module('signinCtrl', ['ionic']);

signinCtrl.controller('signinCtrl', ['$scope', '$http', '$location', '$ionicPopup', function($scope, $http, $location, $ionicPopup) {
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
	    client_id: "1_powyjhqgq28scskw0w04wg8wck8osksgko0ggwgk44kokwo8k",//*/"3_1zm54gls83c0ko4gwk8cg44wsgskkckssg80occ8ssw0ww0wwk",
	    client_secret: "29zjq3ov25hccgk48k84swwo800gccoo08wk40sw48s00gc8kw"//*/"27yd9lyhqj40wkwccgok8848woo80c00ksgocck08s8k80cgwc"
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
    };

}]);


//LIST MODULE

var listCtrl = angular.module('listCtrl', ['ngSanitize', 'ionic']);

/*listCtrl.directive('onLongPress', function($timeout) {
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
 */
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


listCtrl.controller('listCtrl', ['$scope', '$routeParams', '$http', '$window', '$location', '$ionicModal', '$sanitize',function($scope, $routeParams, $http, $window, $location, $ionicModal, $sanitize) {
    $scope.loading = true;
    $scope.showUpDate = "null";
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
	    console.log(file.info["mime_type"]);
	    file.info["icon"] = getImg(file.info);
	});

	$scope.folders = data.folders;
	$scope.files = data.files;
	console.log($scope.folders);
	console.log($scope.files);
	
    }).error(function(data) {
	$scope.loading = false;
    	console.log(data);
    });

    $ionicModal.fromTemplateUrl('modal-menu.html', {
	scope: $scope,
	animation: 'slide-in-up'
    }).then(function(modal) {
	$scope.modalMenu = modal;
    });

    $ionicModal.fromTemplateUrl('modal-info.html', {
	scope: $scope,
	animation: 'slide-in-up'
    }).then(function(modal) {
	$scope.modalInfo = modal;
    });

    $ionicModal.fromTemplateUrl('modal-rename.html', {
	scope: $scope,
	animation: 'slide-in-up'
    }).then(function(modal) {
	$scope.modalRename = modal;
    });

    var fileToUrl = function(pseudo_owner, path, real_path) {
	var url = localStorage.getItem("server.url")+'uploads/' + pseudo_owner + '/' + real_path + path;
	return url;
    };
    var getImg = function(info) {
	if (info.mime_type == false)
	    img = "img/icone/ios7-help-empty.png";
	else if (info.mime_type.search("audio") != -1)
	    img = "img/icone/ios7-musical-notes.png";
	else if (info.mime_type.search("video") != -1)
	    img = "img/icone/ios7-film.png";
	else if (info.mime_type.search("image") != -1)
	{
	    //img = "img/icone/image.png";
	    img = fileToUrl(info.pseudo_owner, info.path, info.real_path);
	}
	else if (info.mime_type.search("epub") != -1
		 || info.mime_type.search("ebook") != -1)
	    img = "img/icone/android-book.png";
	else if (info.mime_type.search("text") != -1
		 || info.mime_type.search("msword") != -1
		 || info.mime_type.search("pdf") != -1)
	    img = "img/icone/document-text.png";
	else if (info.mime_type.search("zip") != -1
		 || info.mime_type.search("tar")!= -1)
	    img = "img/icone/ios7-box.png";
	else
	    img = "img/icone/ios7-help-empty.png";
	return $sanitize(img);
    };
    
    $scope.fileGetUrl = function(pseudo_owner, path, real_path) {
	var url = localStorage.getItem("server.url")+'uploads/' + pseudo_owner + '/' + real_path + path;
	return url;
    };

    $scope.timeToReadable = function(str) {
	if (!str)
	    return "null";
	newstr = str.slice(0, str.search("T"));
	return newstr;
    };

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
	else
	    return nbr + "?";
	
    };

    $scope.itemOnTouchEnd = function(pseudo_owner, path, real_path, name, id) {
	if (justLongPressed == false) {
	    path = fileToUrl(pseudo_owner, path, real_path);
	    console.log(path);
	    MimeType.init();
	    if (MimeType.lookup(path).search("audio") != -1)
		url = "/musicPlayer/"+encodeURIComponent(path)+"/"+name;
	    else if (MimeType.lookup(path).search("video") != -1)
		url = "/videoPlayer/"+encodeURIComponent(path)+"/"+name;
	    else if (MimeType.lookup(path).search("image") != -1)
		url = "/imagePlayer/"+encodeURIComponent(path)+"/"+name+"/"+id;
	    else if (MimeType.lookup(path).search("msword") != -1
		     || MimeType.lookup(path).search("pdf") != -1
		     || MimeType.lookup(path).search("ms-powerpoint") != -1
		     || MimeType.lookup(path).search("text") != -1)
		url = "/documentViewer/"+encodeURIComponent(path)+"/"+name;
	    else
		url = null,
	    console.log(url);
	    if (url != null)
		$location.path(url);
	}
    };

    $scope.folderOnTouchEnd = function(id) {
	$location.path('/list/'+id);
    };

    $scope.itemOnLongPress = function(pseudo_owner, 
				      path, 
				      real_path, 
				      name, 
				      id, 
				      owner, 
				      upDate, 
				      type, 
				      size) {
	//alert("toto");
        justLongPressed = true;
	$scope.showOwner = pseudo_owner;
	$scope.showName = name;
	$scope.showDownloadName = name;// + path.slice(path.lastIndexOf("."));;
	$scope.showId = id;
	$scope.showOwner = owner;
	$scope.showUpDate = upDate;
	$scope.showType = type;
	$scope.showSize = size;
	$scope.showPath = fileToUrl(pseudo_owner, path, real_path);
	//	$scope.$apply();
	$scope.modalMenu.show();
	setTimeout(function(){
            justLongPressed = false;
        }, 500);

    };

    $scope.showInfo = function() {
	$scope.modalMenu.hide();
	$scope.modalInfo.show();
    };

    $scope.showRename = function() {
	$scope.rename = [];
	$scope.rename.name = $scope.showName;
	$scope.modalMenu.hide();
	$scope.modalRename.show();
    };

    $scope.closeModal = function(modalName) {

	if (modalName.search("menu") != -1)
	    $scope.modalMenu.hide();
	else if (modalName.search("info") != -1) {
	    $scope.modalInfo.hide();
	    $scope.modalMenu.show();
	}
	else if (modalName.search("rename") != -1) {
	    $scope.modalRename.hide();
	    $scope.modalMenu.show();
	}
    };

    $scope.saveNewName = function(fileId, rename) {
	console.log("NewName = "+ rename.name);
	console.log("showName = "+ $scope.showName);
	if (rename.name != $scope.showName) {
	    $scope.loading = true;    
	    var myData = $.param({rename: {fromId: $scope.showId,  toName: rename.name}});
	    $http({
		method: 'POST',
		url: localStorage.getItem("server.url")+'app_dev.php/service/1/op/rename',
		data: myData,
		headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	    }).success(function(data) {
		$scope.loading = false;
		$window.location.reload();
	    }).error(function(data) {
		    $scope.loading = false;
    		    console.log(data);
	    });
	}
	$scope.closeModal("rename");
    };
    
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

    $scope.delete = function(fileId) {
        $scope.loading = true;
        var myData = $.param({delete: {deleteId: fileId}});
        $http({
            method: 'POST',
            url: localStorage.getItem("server.url")+'app_dev.php/service/1/op/delete',
            data: myData,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.loading = false;
            $window.location.reload();
        }).error(function(data) {
            $scope.loading = false;
            console.log(data);
        });
        $scope.closeModal("option");
    };
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

imagePlayerCtrl.controller('imagePlayerCtrl', ['$scope', '$routeParams', '$sce', '$http', function($scope, $routeParams, $sce, $http) {
    MimeType.init();
    $scope.id = decodeURIComponent($routeParams.id);
    $scope.mimetype = MimeType.lookup(decodeURIComponent($routeParams.url));
    $scope.srcUrl = $sce.trustAsResourceUrl(decodeURIComponent($routeParams.url));
    $scope.title = $routeParams.name;
    console.log($scope.mimetype);
    $scope.videoHeight = window.innerHeight - 43;
    $scope.videoWidth = window.innerWidth;
    console.log($scope.videoHeight);
    console.log($scope.videoWidth);

    $http.defaults.headers.common.Authorization = 'Bearer '
	+ localStorage.getItem("credential.access_token");

    $scope.delete = function() {
        $scope.loading = true;
        var myData = $.param({delete: {deleteId: $scope.id}});
        $http({
            method: 'POST',
            url: localStorage.getItem("server.url")+'app_dev.php/service/1/op/delete',
            data: myData,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.loading = false;
            window.history.back();
        }).error(function(data) {
            $scope.loading = false;
            console.log(data);
        });
    };
}]);

var documentViewerCtrl = angular.module('documentViewerCtrl', ['ngSanitize']);

documentViewerCtrl.controller('documentViewerCtrl', ['$scope', '$routeParams', '$sce', function($scope, $routeParams, $sce) {
    $scope.srcUrl = $sce.trustAsUrl(decodeURIComponent($routeParams.url));
    console.log( $scope.srcUrl),
    $scope.title = $routeParams.name;
            $scope.loading = true;
    
window.setTimeout(function() {
            $scope.loading = false;
    $('a.embed').gdocsViewer({width: 740, height: 742});
    $('#embedURL').gdocsViewer();
}, 1000);    
}]);

var uploadCtrl = angular.module('uploadCtrl', ['ngSanitize', 'ionic']);

uploadCtrl.controller('uploadCtrl', ['$scope', '$routeParams', '$http', '$window', '$location', '$upload', '$document', function($scope, $routeParams, $http, $window, $location, $upload, $document) {


    $scope.uploadFile = function()
    {
/*	doc = [];
	doc['file'] = file;
	doc['name'] = "toto";*/
	var myData = new FormData();
        var file = document.getElementById("image1").files[0];
        var ajax = new XMLHttpRequest();
	myData = $.param({document: {file: file, name: "test"}});
/*        ajax.upload.addEventListener("progress", myProgressHandler, false);
        ajax.addEventListener("load", myCompleteHandler, false);
        ajax.addEventListener("error", myErrorHandler, false);
        ajax.addEventListener("abort", myAbortHandler, false);
 */       ajax.open("POST", localStorage.getItem("server.url")+'app_dev.php/service/1/op/upload', true);
ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
ajax.setRequestHeader('Authorization', 'Bearer '+ localStorage.getItem("credential.access_token"));
	 ajax.send(myData);
    }



}]);

/*totoCtrl.controller(, ['$scope', '$routeParams', '$http', '$window', '$location', '$upload', function($scope, $routeParams, $http, $window, $location, $upload) {

    $scope.onFileSelect = function($files) {
	//$files: an array of files selected, each file has name, size, and type.
	for (var i = 0; i < $files.length; i++) {
	    var file = $files[i];
	    var myData = {document: {file: file, name: "test"}};//$.param({upload: {name: "test"}});
	    console.log(myData);
	    $scope.upload = $upload.http({
		url: localStorage.getItem("server.url")+'app_dev.php/service/1/op/upload', //upload.php script, node.js route, or servlet url
		method: 'POST',
		headers: {'Content-Type': 'application/x-www-form-urlencoded',
			 'Authorization': 'Bearer '+ localStorage.getItem("credential.access_token")},
		data: myData,
		file: file, // or list of files ($files) for html5 only
		//fillocalStorage.getItem("server.url")+'app_dev.php/service/1/op/rename'eName: 'doc.jpg' or ['1.jpg', '2.jpg', ...] // to modify the name of the file(s)
		// customize file formData name ('Content-Disposition'), server side file variable name. 
		//fileFormDataName: myFile, //or a list of names for multiple files (html5). Default is 'file' 
		// customize how data is added to formData. See #40#issuecomment-28612000 for sample code
		//formDataAppender: function(formData, key, val){}

	    }).progress(function(evt) {
		console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
	    }).success(function(data, status, headers, config) {
		// file is uploaded successfully
		console.log(data);
            }).error(function(data) {
            console.log(data);
        });

	    //.error(...)
	    //.then(success, error, progress); 
	    // access or attach event listeners to the underlying XMLHttpRequest.
	    //.xhr(function(xhr){xhr.upload.addEventListener(...)})
	}
	/* alternative way of uploading, send the file binary with the file's content-type.
	 Could be used to upload files to CouchDB, imgur, etc... html5 FileReader is needed. 
	 It could also be used to monitor the progress of a normal http post/put request with large data
	// $scope.upload = $upload.http({...})  see 88#issuecomment-31366487 for sample code.
    };
}]);*/

