var app = angular.module('myApp', []);

app.controller('myController', function($scope, $http) {
  $http({
    method : "GET",
    url : "http://localhost/~taekyoon/WebTechAssignment/assignment_8th/search.php?name=usc&type=page"
  }).then(function mySucces(response) {
      $scope.search_results = response.data.data;
    }, function myError(response) {
      $scope.search_results = response.statusText;
  });
});
