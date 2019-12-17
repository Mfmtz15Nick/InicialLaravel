/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de agendasHorariosNuevo
|
*/

var app = angular.module('agendasHorariosNuevo', []);

// Controller
app.controller( 'agendasHorariosNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.agenda      = {};
    $scope.horario     = { tm_entrada: '', tm_salida: '' };
    $scope.dias        = [
      {
      id: 1, vc_nombre: 'Lunes'
      },
      {
      id: 2, vc_nombre: 'Martes'
      },
      {
      id: 3, vc_nombre: 'Miercoles'
      },
      {
      id: 4, vc_nombre: 'Jueves'
      },
      {
      id: 5, vc_nombre: 'Viernes'
      },
      {
      id: 6, vc_nombre: 'Sabado'
      },
      {
      id: 7, vc_nombre: 'Domingo'
      }
    ];
    $scope.flags    = { editar: false };

// Scope Functions
    $scope.regresar = function() {
        $state.go('agendasHorarios', { id : $scope.agenda.id });
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        if( $scope.horario.tm_entrada >= $scope.horario.tm_salida){
          $message.warning('La hora de entrada debe ser menor a la hora de salida.');
          return;
        }

        $loading.show();

        if( !$scope.flags.editar ) {
            ModelService.custom('post', 'api/agenda/' + $scope.agenda.id + '/horarios', $scope.horario)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('agendasHorarios', { id : $scope.agenda.id });
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El horario de la agenda ' + $scope.agenda.vc_nombre + ', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else {
            ModelService.update($scope.agenda)
                .success(function (res) {
                    $message.success(res.texto);
                    $loading.hide();
                    $state.go('agendas');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El agenda '+$scope.agenda.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){

        // Definir Modelo
        ModelService.addModel('agendas');

        // Verificar proceso Agregar o Editar
        $util.stateParams(function(){

            $loading.show();

            // $scope.flags.editar = true;

            console.log($stateParams);

            ModelService.custom('get', 'api/agendas/'+ $stateParams.id + '/horarios/nuevo' )
                .success(function(res){
                    $scope.agenda                = res;
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning("No se pudo obtener el registro.");
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        });
    };

// Begin Module
    $scope.init();

}]);
