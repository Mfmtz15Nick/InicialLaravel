/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| Sergio Carreon
| - Controllador de la Vista de clientesEventos
|
*/

var app = angular.module('clientesEventos', []);

// Controller
app.controller( 'clientesEventosController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.eventos = [];
    $scope.cliente   = {};
    $scope.cargando = false;

// Scope Funciones

    $scope.regresar = function() {
        $state.go('clientes');
    };

    $scope.actualizar = function() {
        $scope.init();
    };

    $scope.agregar = function(nu_dia, nu_mes) {
        $state.go('clientesEventosNuevo', { id : $scope.cliente.id });
    };

    $scope.editar = function( evento ) {
        $state.go('clientesEventosEditar', { id : $scope.cliente.id, idDia: evento.id  });
    };

    $scope.eliminar = function( evento, eventoDia ) {
    
    
      $message.confirm({
          text    : '¿Estás seguro de eliminar el evento con id ' +evento.i+'?',
          callback : function( msg ){
              $loading.show();
              ModelService.custom('delete', 'api/clientes/eventos/'+ evento.id )
                  .success(function(result){
                      msg.close();
                      $message.success(result.texto);
                  })
                  .error(function (error) {
                      $message.warning(error.texto);
                  })
                  .finally(function(){
                      $loading.hide();
                  });
          }
      });
    };

    $scope.init = function() {

      ModelService.addModel('clientes');

      $scope.cargando = true;

      // Verificar proceso Agregar o Editar
      $util.stateParams(function(){

        ModelService.custom('get', 'api/clientes/'+ $stateParams.id + '/eventos' )
            .success(function(res){
                $scope.cliente   = res.cliente;
                $scope.eventos = res.eventos;
            })
            .error(function (error) {
                $message.warning(error.texto);
            })
            .finally(function(){
                $scope.cargando = false;
            });
      });
    };

// Iniciar Controller
    $scope.init();
}]);
