/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| Sergio Carreon
| - Controllador de la Vista de agendasHorarios
|
*/

var app = angular.module('agendasHorarios', []);

// Controller
app.controller( 'agendasHorariosController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.horarios = [];
    $scope.agenda   = {};
    $scope.cargando = false;

// Scope Funciones

    $scope.regresar = function() {
        $state.go('agendas');
    };

    $scope.actualizar = function() {
        $scope.init();
    };

    $scope.agregar = function(nu_dia) {
        $state.go('agendasHorariosNuevo', { id : $scope.agenda.id });
    };

    $scope.editar = function( horario ) {
        $state.go('agendasHorariosEditar', { id : $scope.agenda.id, idDia: horario.id  });
    };

    $scope.eliminar = function( horario, horarioDia ) {
      $message.confirm({
          text    : '¿Estás seguro de eliminar el horario de '+ horarioDia.tm_entrada  +' a '+ horarioDia.tm_salida +' del dia ' +horario.vc_nombre+'?',
          callback : function( msg ){
              $loading.show();
              ModelService.custom('delete', 'api/agendas/horarios/'+ horarioDia.id )
                  .success(function(result){
                      msg.close();
                      var posicionDia     = $util.getPosition($scope.horarios, 'id', horario.id);
                      var posicionHorario = $util.getPosition($scope.horarios[posicionDia].horarios, 'id', horarioDia.id);
                      $scope.horarios[posicionDia].horarios.splice( posicionHorario, 1 );
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

      ModelService.addModel('agendas');

      $scope.cargando = true;

      // Verificar proceso Agregar o Editar
      $util.stateParams(function(){

        ModelService.custom('get', 'api/agendas/'+ $stateParams.id + '/horarios' )
            .success(function(res){
                $scope.agenda   = res.agenda;
                $scope.horarios = res.horarios;
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
