var userControllers = angular.module('userControllers', ['ngMaterial']);

userControllers.controller('UserCtrl', ['$scope', '$http', '$cookies', '$mdDialog', '$location', '$routeParams', '$route',
    function ($scope, $http, $cookies, $mdDialog, $location, $routeParams, $route) {
        //redirect if not loggged in
        if ($scope.data.userId == 0)
            $location.path('/user/subscribe');

        //get user
        var currentUser = $routeParams.id;
        $scope.data.selectedIndex = 3;
        $scope.user = $scope.data.user[currentUser - 1];

        //get its plugin
        $scope.pluginList = $scope.data.module;

        //init new module to null
        $scope.newModule = {};

        $scope.updateUser = function () {
            if ($scope.userForm.$valid == true)
                console.log($scope.user);
        };

        $scope.changePassword = function () {
            $mdDialog.show({
                controller: 'PasswordCtrl',
                templateUrl: 'partials/password.html',
                targetEvent: "mouse",
            })
            .then(function (answer) {
                $scope.alert = 'You said the information was "' + answer + '".';
            }, function () {
                $scope.alert = 'You cancelled the dialog.';
            });
        };

        $scope.deleteAccount = function (ev) {
            var confirm = $mdDialog.confirm()
              .title('Are you sure you want to delete your account?')
              .content('All your data and plugins will be deleted')
              .ariaLabel('Delete account')
              .ok('Yes')
              .cancel('No')
              .targetEvent("mouse");
            $mdDialog.show(confirm).then(function () {
                //delete
                $scope.logout();
            }, function () {
                //do nothing
            });
        };

        $scope.saveModule = function () {
            console.log($scope.newModule);
            var selected_picture = document.getElementById('picture').files[0];
            var pictureReader = new FileReader();
            pictureReader.readAsDataURL(selected_picture);
            pictureReader.onloadend = function () {

                $scope.src = pictureReader.result;
                console.log($scope.src);

                var selected_file = document.getElementById('plugin').files[0];
                var pluginReader = new FileReader();
                pluginReader.readAsText(selected_file);
                pluginReader.onloadend = function () {
                    console.log(encodeURIComponent(pluginReader.result));

                    var path = $location.path();
                    $route.reload();
                }
                $scope.$apply();
            };

            

        };

        $scope.deleteModule = function (id, name) {
            var confirm = $mdDialog.confirm()
             .title('Are you sure you want to delete this module?')
             .content(name + ': will no longer be deleted from the store')
             .ariaLabel('Delete module')
             .ok('Yes')
             .cancel('No')
             .targetEvent("mouse");
            $mdDialog.show(confirm).then(function () {
                //delete
            }, function () {
                //do nothing
            });
        };

    }]);

userControllers.controller('PasswordCtrl', ['$scope', '$http', '$mdDialog',
  function ($scope, $http, $mdDialog) {
      $scope.password = {
          'old': '',
          'new': '',
          'repeat': ''
      };

      $scope.$watch("password.repeat", function () {
          $scope.passwordForm.repeat.$valid = $scope.password.new == $scope.password.repeat;
          $scope.passwordForm.repeat.$setValidity("required", $scope.password.new == $scope.password.repeat);
          console.log($scope.password.new == $scope.password.repeat);
      });
      $scope.hide = function () {
          $mdDialog.hide();
      };
      $scope.cancel = function () {
          $mdDialog.cancel();
      };
      $scope.save = function () {
          console.log($scope.passwordForm);
          if ($scope.passwordForm.$valid == true) //make request here
              $mdDialog.hide($scope.password.new);
      };
  }]);

userControllers.controller('SubscribeCtrl', ['$scope', '$http', '$location', '$cookies',
  function ($scope, $http, $location, $cookies) {
      $scope.data.selectedIndex = 0;
      if ($scope.data.userId != 0)
          $location.path('/');
      $scope.login = function () {
          //set authentification
          $scope.data.userId = 1;
          $cookies.token = "toto";
          $location.path('/');
      };
      $scope.register = function () {
          //register user
          $scope.data.userId = 1;
      };
  }]);