var moduleControllers = angular.module('moduleControllers', ['acsilModule']);

moduleControllers.controller('ModuleListCtrl', ['$scope', '$http', 'Module',
  function ($scope, $http, Module) {
      $scope.data.selectedIndex = 1;

      $scope.plugins = Module.query();

      //get module List
      var result = Module.query();
      result.$promise.then(function (data) {
          $scope.PluginList = angular.copy(data);
          $scope.saveFullList = angular.copy(data);
      });

      console.log($scope.saveFullList);
      $scope.search = function (keywords) {
          console.log($scope.saveFullList);
          if (keywords == null || keywords == "") {
              $scope.PluginList = $scope.saveFullList;
              return;
          }
          $scope.PluginList = [];
          var j = 0;
          for (var i = 0; i < $scope.saveFullList.length; i++) {
              if ($scope.saveFullList[i].keywords.toLowerCase().trim().indexOf(keywords.toLowerCase().trim()) != -1
                  || keywords.toLowerCase().trim().indexOf($scope.saveFullList[i].keywords.toLowerCase().trim()) != -1) {
                  $scope.PluginList[j] = angular.copy($scope.saveFullList[i]);
                  j++;
              }
          }
      }
  }]);
moduleControllers.controller('ModuleViewCtrl', [
    '$scope', '$http', '$routeParams', '$sce', 'Module',
  function ($scope, $http, $routeParams, $sce, Module) {
      $scope.data.selectedIndex = 1;
      var currentPlugin = $routeParams.id;
      //get module by id
      $scope.plugin = Module.get({id: currentPlugin});

      $scope.buildURL = function (content) {
          return $sce.trustAs($sce.RESOURCE_URL, "data:text/plain;charset=utf-8, " + content);
      }

  }]);
