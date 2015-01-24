var moduleControllers = angular.module('moduleControllers', ['acsilstore']);

moduleControllers.controller('ModuleListCtrl', ['$scope', '$http', 'Module',
  function ($scope, $http, Module) {
      $scope.data.selectedIndex = 1;

      $scope.plugins = Module.query();

      //get module List
      $scope.PluginList = angular.copy($scope.data.module);
      $scope.saveFullList = angular.copy($scope.data.module);
      console.log($scope.saveFullList);
      $scope.search = function (keywords) {
          console.log($scope.saveFullList);
          if (keywords == null || keywords == "") {
              $scope.PluginList = $scope.data.module;
              return;
          }
          $scope.PluginList = [];
          var j = 0;
          for (var i = 0; i < $scope.saveFullList.length; i++) {
              if ($scope.saveFullList[i].keywords.toLowerCase().trim().indexOf(keywords.toLowerCase().trim()) != -1) {
                  $scope.PluginList[j] = angular.copy($scope.saveFullList[i]);
                  j++;
              }
          }
      }
  }]);
moduleControllers.controller('ModuleViewCtrl', ['$scope', '$http', '$routeParams', '$sce',
  function ($scope, $http, $routeParams, $sce) {
      $scope.data.selectedIndex = 1;
      var currentPlugin = $routeParams.id - 1;
      //get module by id
      $scope.plugin = $scope.data.module[currentPlugin];

      $scope.buildURL = function (content) {
          return $sce.trustAs($sce.RESOURCE_URL, "data:text/plain;charset=utf-8, " + content);
      }

  }]);
