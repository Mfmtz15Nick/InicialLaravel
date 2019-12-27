/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de eventosNuevo
|
*/

var app = angular.module('eventosNuevo', []);

// Controller
app.controller( 'eventosNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.evento     = { vc_nombre: "" };
    $scope.agendas  = [];
    $scope.flags    = { editar: false };

// Scope Functions
    $scope.regresar = function() {
        $state.go('eventos');
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        $scope.evento.dt_fecha.setHours($scope.evento.tm_evento.getHours());
        $scope.evento.dt_fecha.setMinutes($scope.evento.tm_evento.getMinutes());

        $loading.show();

        if( !$scope.flags.editar ) {
            ModelService.add($scope.evento)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('eventos');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('La evento '+$scope.evento.vc_nombre+', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else {
            ModelService.update($scope.evento)
                .success(function (res) {
                    $message.success(res.texto);
                    $loading.hide();
                    $state.go('eventos');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('La evento '+$scope.evento.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){

        // Definir Modelo
        ModelService.addModel('eventos');

        $loading.hide();

        ModelService.create()
            .success(function(res){

                $scope.agendas  = angular.copy(res);

                // Verificar proceso Agregar o Editar
                $util.stateParams(function(){

                    $scope.flags.editar = true;

                    console.log($stateParams);

                    ModelService.edit( $stateParams.id )
                        .success(function(res){
                            console.log(res);
                            $scope.evento               = res;
                            $scope.evento.id_agenda     = String($scope.evento.id_agenda);
                            var dt_fecha              = res.dt_fecha.date;
                            $scope.evento.dt_fecha      = new Date(dt_fecha);
                            $scope.evento.tm_evento       = new Date(dt_fecha);
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
    };

// Begin Module
    $scope.init();

}]);
