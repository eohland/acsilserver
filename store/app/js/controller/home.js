var homeControllers = angular.module('homeControllers', []);

homeControllers.controller('HomeCtrl', ['$scope', '$http',
  function ($scope, $http) {
      $scope.data.selectedIndex = 0;

      $scope.PopularList = $scope.data.module;
      $scope.RecentList = $scope.data.module;

  }]);