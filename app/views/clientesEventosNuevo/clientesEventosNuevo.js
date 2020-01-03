/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de clientesEventosNuevo
|
*/

var app = angular.module('clientesEventosNuevo', []);

// Controller
app.controller( 'clientesEventosNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.cliente      = {};
    $scope.tiposEventos = [];
    $scope.evento     = { tm_entrada: '', tm_salida: '', nu_dia:'', id_evento:'' };
    $scope.meses        = [
      {
      id: 1, vc_nombre: 'Enero'
      },
      {
      id: 2, vc_nombre: 'Febrero'
      },
      {
      id: 3, vc_nombre: 'Marzo'
      },
      {
      id: 4, vc_nombre: 'Abril'
      },
      {
      id: 5, vc_nombre: 'Mayo'
      },
      {
      id: 6, vc_nombre: 'Junio'
      },
      {
      id: 7, vc_nombre: 'Julio'
      },
      {
      id: 8, vc_nombre: 'Agosto'
      },
      {
      id: 9, vc_nombre: 'Septiembre'
      },
      {
      id: 10, vc_nombre: 'Octubre'
      },
      {
      id: 11, vc_nombre: 'Noviembre'
      },
      {
      id: 12, vc_nombre: 'Diciembre'
      }
    ];
    $scope.flags    = { editar: false };

// Scope Functions
    $scope.regresar = function() {
        $state.go('clientesEventos', { id : $scope.cliente.id });
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        if( $scope.evento.tm_entrada >= $scope.evento.tm_salida){
          $message.warning('La hora de entrada debe ser menor a la hora de salida.');
          return;
        }

        $loading.show();

        if( !$scope.flags.editar ) {// CREATE
            console.log('vamo a guardarlo' + $stateParams.id)
            ModelService.custom('post', 'api/clientes/' + $stateParams.id + '/eventos', $scope.evento)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('clientesEventos', { id : $scope.cliente.id });
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El evento de la cliente ' + $scope.cliente.vc_nombre + ', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else { // EDITAR
            console.log('vamo a editar puej')
            ModelService.custom('put', 'api/clientes/eventos/' + $scope.evento.id, $scope.evento)
                .success(function (res) {
                    $message.success(res.texto);
                    $loading.hide();
                    $state.go('clientesEventos', { id : $scope.cliente.id });
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El evento de la cliente '+$scope.cliente.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){

        // Definir Modelo
        ModelService.addModel('clientes');

        // Verificar proceso Agregar o Editar
        $util.stateParams(function(){
            console.log('verificamos para actualizar o agregar')

            $loading.show();

            console.log($stateParams);

            if ($stateParams.idDia) { // ES UNA ACTUALIZACION

              $scope.flags.editar = true;

              ModelService.custom('get', 'api/clientes/eventos/' + $stateParams.idDia )
                  .success(function(res){
                    console.log(res);
                    $scope.cliente  = res.cliente;
                    $scope.evento = res;
                    $scope.tiposEventos           = res.tiposEventos;
                    $scope.evento.tm_entrada = new Date(res.tm_entrada.date);
                    $scope.evento.tm_salida  = new Date(res.tm_salida.date);
                    $scope.evento.nu_dia     = ''+res.nu_dia;
                    $scope.evento.nu_mes     = ''+res.nu_mes;
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
            }
            else { // SE AGREGA UNO NUEVO
                console.log('guardamos')
              ModelService.custom('get', 'api/clientes/'+ $stateParams.id + '/eventos/nuevo' )
                  .success(function(res){
                      $scope.cliente                = res;
                      $scope.tiposEventos           = res.tiposEventos;
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
            }
        });
    };

// Begin Module
    $scope.init();

}]);
