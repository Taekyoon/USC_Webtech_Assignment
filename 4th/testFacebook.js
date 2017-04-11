var facebookService = angular.module('facebookService', []);

$window.fbAsyncInit = function() {
  FB.init({
    appId: '225410634597276',
    status: true,
    cookie: true,
    xfbml: true,
    version: 'v2.4'
  });
};

facebookService.factory('facebookService', function($q) {
  return {
      getMyLastName: function() {
          var deferred = $q.defer();
          FB.api('/me', {
              fields: 'last_name'
          }, function(response) {
              if (!response || response.error) {
                  deferred.reject('Error occured');
              } else {
                  deferred.resolve(response);
              }
          });
          return deferred.promise;
      }
   }
});

facebookService.controller("testController", function($scope) {
  $scope.getMyLastName = function() {
   facebookService.getMyLastName()
     .then(function(response) {
       $scope.last_name = response.last_name;
     }
   );
  };
})
