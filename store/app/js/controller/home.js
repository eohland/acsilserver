var homeControllers = angular.module('homeControllers', ['acsilModule']);

homeControllers.controller('HomeCtrl', ['$scope', '$http', 'Module',
  function ($scope, $http, Module) {
      $scope.data.selectedIndex = 0;

      var modulePromise = Module.query();
      modulePromise.$promise.then(function (moduleList) {
          $scope.PopularList = angular.copy(moduleList).slice(0, 4);
          $scope.RecentList = moduleList.sort(function (a, b) { return b.update_date - a.update_date }).slice(0, 4);
      });

  }]);