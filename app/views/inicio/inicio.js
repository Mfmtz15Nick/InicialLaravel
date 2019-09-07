/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de Inicio
|
*/

var app = angular.module('inicio', []);

// Controller
app.controller( 'inicioController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables

// Scope Funciones
	$scope.init = function(){

	}

// Iniciar Controller
	$scope.init();
}]);
