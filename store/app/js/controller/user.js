﻿var userControllers = angular.module('userControllers', [
    'ngMaterial', 'acsilModule', 'acsilUser']);

userControllers.controller('UserCtrl', [
    '$scope', '$http', '$mdDialog',
    '$location', '$routeParams', '$route',
    'User',
    function ($scope, $http, $mdDialog,
      $location, $routeParams, $route,
      User) {
        //set selected tab
        $scope.data.selectedIndex = 3;

        //get currendìt user id
        var currentUserId = $routeParams.id;

        //redirect if not loggged in
        if (currentUserId == 0)
            $location.path('/user/subscribe');

        //get current user data
        $scope.user = {};
        var userPromise = User.get({ id: currentUserId });
        userPromise.$promise.then(function (userData) {
            $scope.user = userData;
            //get user moduleList
        })

        //get its plugin
        $scope.pluginList = $scope.data.module;

        //init new module to null
        $scope.newModule = {};

        $scope.updateUser = function () {
            if ($scope.userForm.$valid != true)
                return;
            var update = User.update(new User({
                id: $scope.user.id,
                login: $scope.user.login,
                password: $scope.user.password,
                email: $scope.user.email,
                display_name: $scope.user.display_name,
                create_date: $scope.user.create_date,
                update_date: Date.now(),
            }));

            update.$promise.then(function () {
                $mdDialog.show(
                    $mdDialog.alert()
                      .title('Success')
                      .content('Informations changed with success.')
                      .ariaLabel('Informations success')
                      .ok('OK')
                      .targetEvent("mouse")
                  ).then(function () {
                      $route.reload();
                  });
            });
        };

        $scope.changePassword = function () {
            $mdDialog.show({
                controller: 'PasswordCtrl',
                templateUrl: 'partials/password.html',
                targetEvent: "mouse",
            })
            .then(function (newPassword) {
                //update user
                var update = User.update(new User({
                    id: $scope.user.id,
                    login: $scope.user.login,
                    password: CryptoJS.SHA512(newPassword).toString(CryptoJS.enc.Hex),
                    email: $scope.user.email,
                    display_name: $scope.user.display_name,
                    create_date: $scope.user.create_date,
                    update_date: Date.now(),
                }));

                update.$promise.then(function () {
                    $mdDialog.show(
                        $mdDialog.alert()
                          .title('Success')
                          .content('Password changed with success.')
                          .ariaLabel('Password success')
                          .ok('OK')
                          .targetEvent("mouse")
                      ).then(function () {
                          $route.reload();
                      });
                });
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
                var deletePromise = User.delete({id: $scope.user.id});

                deletePromise.$promise.then(function () {
                    $mdDialog.show(
                        $mdDialog.alert()
                          .title('Success')
                          .content('Your account was deleted with success.')
                          .ariaLabel('delet success')
                          .ok('OK')
                          .targetEvent("mouse")
                      ).then(function () {
                          $scope.logout();
                      });
                });
                
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
                    Module.create(new Module({
                        name: $scope.newModule.name,
                        author_id: $scope.data.userId,
                        keywords: $scope.newModule.keywords,
                        version: $scope.newModule.version,
                        description: $scope.newModule.description,
                        picture: $scope.src,
                        content: encodeURIComponent(pluginReader.result),
                        create_date: Date.now(),
                        update_date: Date.now()
                    }));
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

userControllers.controller('PasswordCtrl', ['$scope', '$http', '$mdDialog', '$routeParams', 'User',
    function ($scope, $http, $mdDialog, $routeParams, User) {
      $scope.password = {
          'old': '',
          'new': '',
          'repeat': ''
      };

      var currentUserId = $routeParams.id;
      $scope.user = User.get({ id: currentUserId });

      $scope.$watch("password.old", function () {
          $scope.passwordForm.repeat.$valid = CryptoJS.SHA512($scope.password.old).toString(CryptoJS.enc.Hex) == $scope.user.password;
          $scope.passwordForm.repeat.$setValidity("required", CryptoJS.SHA512($scope.password.old).toString(CryptoJS.enc.Hex) == $scope.user.password);
          console.log(CryptoJS.SHA512($scope.password.old).toString(CryptoJS.enc.Hex) == $scope.user.password);
      });

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

userControllers.controller('SubscribeCtrl', ['$scope', '$http', '$location', '$cookieStore', 'User', '$mdDialog', '$route',
    function ($scope, $http, $location, $cookieStore, User, $mdDialog, $route) {

        $scope.newUser = {
            'login': '',
            'password': '',
            'repeat': '',
            'email': '',
            'display_name': ''
        };

        $scope.data.selectedIndex = 0;
        $scope.register = false;

        console.log($scope.data.userId);
        if ($scope.data.userId != 0 && $scope.data.userId != null)
            $location.path('/');


        $scope.login = function () {
            //set authentification
            $scope.data.userId = 1;
            $cookieStore.put("token", "toto");
            $location.path('/');
        };


        $scope.subscribe = function () {
            //register user

            $scope.data.userId = 1;
            console.log($scope.subscribeForm);
            if ($scope.subscribeForm.$valid == true) { //make request here
                var subscribe = User.create(new User({
                    login: $scope.newUser.login,
                    password: CryptoJS.SHA512($scope.newUser.password).toString(CryptoJS.enc.Hex),
                    email: $scope.newUser.email,
                    display_name: $scope.newUser.display_name,
                    create_date: Date.now(),
                    update_date: Date.now()
                }));

                subscribe.$promise.then(function () {
                    $mdDialog.show(
                        $mdDialog.alert()
                          .title('Success')
                          .content('Register success. You can now log in.')
                          .ariaLabel('Register success')
                          .ok('OK')
                          .targetEvent("mouse")
                      ).then(function () { $route.reload(); });
                });
            }

        };

        $scope.$watch("newUser.repeat", function () {
            if ($scope.register) {
                $scope.subscribeForm.repeat.$valid = $scope.newUser.password == $scope.newUser.repeat;
                $scope.subscribeForm.repeat.$setValidity("required", $scope.newUser.password == $scope.newUser.repeat);
            }
            console.log($scope.newUser.password == $scope.newUser.repeat);
        });
    }]);
