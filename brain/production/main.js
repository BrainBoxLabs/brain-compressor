var myapp = angular.module('myapp',[]);

var BarController = 'Hello!';

myapp.controller('FooController',function($scope){

    $scope.first_name = 'Andrew Wood';

    $scope.buttonPressed = function(){
        alert($scope.first_name);
    }

});

