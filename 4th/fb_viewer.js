var fb_search = angular.module('fb_search', ['ngRoute', 'ngAnimate'])

fb_search.config(function($routeProvider){
  $routeProvider
  .when('/:searchType', {
    templateUrl: 'results.html',
    controller: 'fb_search_results',
    resolve: {
      fbDataMessage: function(fbService, favoriteStorer) {
        if (fbService.search_data != null) {
          var store = { data: favoriteStorer.get()}
          fbService.search_data.favorite = angular.copy(store)
        }
      }
    }
  })
  .when('/:searchType/detail', {
    templateUrl: 'details.html',
    controller: 'fb_detail_result',
  })
})

fb_search.controller('fb_search_results', ['dataShare', 'favoriteStorer', '$scope', 'fbService', '$routeParams', '$location', '$animate',
  function(dataShare, favoriteStorer, $scope, fbService, $routeParams, $location, $animate) {
    $scope.resultExist = false
    console.log('work!' + $routeParams.searchType)
    dataShare.share('resultType', $routeParams.searchType)

    $scope.$on('SearchBroadcast', function() {
        console.log('data in')
        $scope.showNext = false
        $scope.showPrev = false
        $scope.type = $routeParams.searchType
        if ($scope.type == 'favorite') {
          $scope.favoritePage = true
          $scope.resultExist = true
        }

        if (fbService.search_data[$scope.type] && $scope.type !== '') {
          dataShare.share('offProgress', '')
          $scope.resultExist = true
          if ($scope.type != 'favorite') {
            for (var i = 0; i < fbService.search_data[$scope.type].data.length; i++) {
              if (favoriteStorer.find(fbService.search_data[$scope.type].data[i])) {
                fbService.search_data[$scope.type].data[i].favorite = true
              } else {
                fbService.search_data[$scope.type].data[i].favorite = false
              }
            }

            if (typeof fbService.search_data[$scope.type].paging != 'undefined') {
              $scope.move_url = fbService.search_data[$scope.type].paging
              if(typeof $scope.move_url.next !== 'undefined') {
                $scope.showNext = true
              }
              if (typeof $scope.move_url.previous !== 'undefined') {
                $scope.showPrev = true
              }
            }
          }
          $scope.search_results = fbService.search_data[$scope.type].data
          $scope.resultExist = true
        }
    });
    $scope.$on('clearResult', function() {
      $scope.showNext = false
      $scope.showPrev = false
      $scope.resultExist = false
    })
    $scope.move = function (next, url, type) {
      if (next) {
        fbService.move(url.next, type)
      } else {
        fbService.move(url.previous, type)
      }
    }
    $scope.set_favorite = function(data, type, index) {
      if (!data.favorite) {
        favoriteStorer.add(data.picture.data.url, data.name, data.id, type)
        $scope.search_results[index].favorite = true
      } else {
        favoriteStorer.remove(data, type)
        $scope.search_results[index].favorite = false
      }
    }
    $scope.delete_favorite = function(data, index) {
      favoriteStorer.remove(data)
      var store = { data: favoriteStorer.get()}
      fbService.search_data.favorite = angular.copy(store)
      fbService.get()
    }
    $scope.get_detail = function(data, type) {
      dataShare.share('doSlideFromLeft', '')
      $scope.resultExist = false
      var sendData = {
        id: data.id,
        name: data.name,
        picture: $scope.favoritePage ? data.picture : data.picture.data.url
      }
      fbService.detail(angular.copy(sendData))
      $location.path('/' + type + '/detail')
    }
    fbService.get()
  }
])

fb_search.controller('fb_detail_result', ['favoriteStorer', 'dataShare', '$scope', '$location', 'fbService', '$routeParams',
  function (favoriteStorer, dataShare, $scope, $location, fbService, $routeParams) {
    if (fbService.detail_data){
      $scope.showDetail = true
    }
    dataShare.share('resultType', $routeParams.searchType)

    $scope.$on('DetailBroadcast', function() {
      $scope.showDetail = true
      $scope.albumExist = false
      $scope.postExist = false
      $scope.albumErrorExist = false
      $scope.postErrorExist = false

      favoriteStorer.get()
      if (favoriteStorer.find(fbService.detail_data.user)) {
        $scope.favorite = true
      }

      if (fbService.detail_data.albums) {
        $scope.albumExist = true
        var albums = fbService.detail_data.albums.data

        albums[0].showAlbum = true
        for (var i = 1; i < albums.length; i++) {
          albums[i].showAlbum = false
        }
        $scope.albums = albums
      } else {
        $scope.albumErrorExist = true
      }

      if (fbService.detail_data.posts) {
        $scope.postExist = true
        $scope.posts = fbService.detail_data.posts.data
      } else {
        $scope.postErrorExist = true
      }

      $scope.user = fbService.detail_data.user
    })
    $scope.show = function(album, albums) {
      var currentState = album.showAlbum

      for (var i = 0; i < albums.length; i++) {
        albums[i].showAlbum = false
      }
      if (!currentState) {
        album.showAlbum = true
      }
    }
    $scope.back = function() {
      dataShare.share('doSlideFromRight', '')
      var type = $routeParams.searchType

      $location.path('/' + type)
      $location.replace();
    }
    $scope.set_favorite = function(favorite, data) {
      if (!favorite) {
        favoriteStorer.add(data.picture, data.name, data.id, $routeParams.searchType)
        $scope.favorite = true
      } else {
        favoriteStorer.remove(data, $routeParams.searchType)
        $scope.favorite = false
      }
    }
    $scope.share_this = function(data) {
      FB.ui({
        method: 'share',
        display: 'popup',
        href: 'https://www.facebook.com/USCTrojans/',
      }, function(response){})
    }
  }
])

fb_search.controller('fb_search_form', ['dataShare', '$scope', '$location', 'fbService', '$routeParams',
  function (dataShare, $scope, $location, fbService, $routeParams) {
      var selected = {
        user: false,
        page: false,
        event: false,
        place: false,
        group: false,
        favorite: false,
      }

      if (typeof $routeParams.searchType == 'undefined') {
        selected.user = true
      }

      $scope.showSearchProgress = false
      $scope.selected = selected
      $scope.submit = function() {
        dataShare.share('noSlides', '')
        var type = 'user'
        if ($routeParams.searchType) {
          type = $routeParams.searchType
        }
        fbService.clear()
        fbService.search($scope.text)

        $scope.showSearchProgress = true
        dataShare.share('clearResult', '')
        $location.path('/' + type)
        $location.replace();
      }
      $scope.main = function() {
        fbService.clear()
        $location.path('/')
        $location.replace();
      }
      $scope.$on('resultType', function() {
        var type = 'user'
        if (typeof dataShare.data != 'undefined') {
          type = dataShare.data
        }

        for (var key in selected) {
          selected[key] = false
        }
        selected[type] = true
        $scope.selected = selected
      })
      $scope.$on('offProgress', function() {
        $scope.showSearchProgress = false
      })
      $scope.$on('doSlideFromLeft', function() {
        $scope.doSlideToLeft = true
        $scope.doSlideToRight = false
      })
      $scope.$on('doSlideFromRight', function() {
        $scope.doSlideToRight = true
        $scope.doSlideToLeft = false
      })
      $scope.$on('noSlides', function() {
        $scope.doSlideToRight = false
        $scope.doSlideToLeft = false
      })
  }
])

fb_search.factory('fbService', function($http, $rootScope) {
  var broadcastItem = function(tag) {
    $rootScope.$broadcast(tag)
  }

  var request = function(url, data) {
    return $http({
        method : "GET",
        url : url
      }).then(function mySuccess(response) {
          console.log(response)
          return response.data
        }, function myError(response) {
          console.log(response)
      })
  }

  var fbService = {
    search_data: {
      user: null,
      page: null,
      event: null,
      place: null,
      group: null,
      favorite: null
    },
    detail_data: null,
    search: function(keyword = '') {
      url = "http://localhost/~taekyoon/WebTechAssignment/assignment_8th/search.php?name=" + keyword
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          url += "&location=" + position.coords.latitude + "," + position.coords.longitude
        })
      } else {
          console.log("Geolocation is not supported by this browser.")
      }
      request(url).then(function(data) {
        fbService.search_data.user = angular.copy(data.user)
        fbService.search_data.page = angular.copy(data.page)
        fbService.search_data.event = angular.copy(data.event)
        fbService.search_data.place = angular.copy(data.place)
        fbService.search_data.group = angular.copy(data.group)
        broadcastItem('SearchBroadcast')
      })
    },
    move: function(url, type) {
      request(url).then(function(data) {
        fbService.search_data[type] = angular.copy(data)
        broadcastItem('SearchBroadcast')
      })
    },
    detail: function(receivedData) {
      url = "http://localhost/~taekyoon/WebTechAssignment/assignment_8th/detail.php?userid=" + receivedData.id
      request(url).then(function(data) {
        var detail = {
          posts: null,
          albums: null,
          user: null
        }
        detail.albums = typeof data.albums != 'undefined' ? data.albums : null
        detail.posts = typeof data.posts != 'undefined' ? data.posts : null
        detail.user = receivedData
        fbService.detail_data = angular.copy(detail)
        broadcastItem('DetailBroadcast')
      })
    },
    get: function() {
      broadcastItem('SearchBroadcast')
    },
    clear: function() {
      fbService.search_data = []
      fbService.detail_data = null
    }
  }
  return fbService
})

fb_search.factory('dataShare', function($rootScope) {
  var dataShare = {
    data: null,
    share: function(tag ,data) {
      dataShare.data = data
      $rootScope.$broadcast(tag)
    }
  }

  return dataShare
})

fb_search.factory('favoriteStorer', function() {
  var FAVORITE_DATA = 'FAVORITE_DATA'
  var favoriteStorer = {
    data: [],
    _saveToLocalStorage: function(data) {
      localStorage.setItem(FAVORITE_DATA, JSON.stringify(data))
    },
    _getFromLocalStorage: function() {
      return JSON.parse(localStorage.getItem(FAVORITE_DATA))
    },
    get: function() {
      angular.copy(favoriteStorer._getFromLocalStorage(),favoriteStorer.data)
      return favoriteStorer.data
    },
    add: function(picture, name, id, type) {
      var newFavorite = {
        id: id,
        name: name,
        picture: picture,
        type: type
      }
      favoriteStorer.data.push(newFavorite)
      favoriteStorer._saveToLocalStorage(favoriteStorer.data)
    },
    remove: function(data) {
      var idx = favoriteStorer.data.findIndex(function(item) {
        return item.id == data.id
      })
      if (idx > -1) {
        favoriteStorer.data.splice(idx, 1)
      }
      favoriteStorer._saveToLocalStorage(favoriteStorer.data)
      favoriteStorer.get()
    },
    find: function(data) {
      var idx = favoriteStorer.data.findIndex(function(item) {
        return item.id == data.id
      })

      return idx > -1 ? true : false
    }
  }

  return favoriteStorer
})
