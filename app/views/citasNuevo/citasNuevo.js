/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de citasNuevo
|
*/

var app = angular.module('citasNuevo', []);

// Controller
app.controller( 'citasNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.cita     = { vc_nombre: "" };
    $scope.agendas  = [];
    $scope.flags    = { editar: false };

// Scope Functions
    $scope.regresar = function() {
        $state.go('citas');
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        $scope.cita.dt_fecha.setHours($scope.cita.tm_cita.getHours());
        $scope.cita.dt_fecha.setMinutes($scope.cita.tm_cita.getMinutes());

        $loading.show();

        if( !$scope.flags.editar ) {
            ModelService.add($scope.cita)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('citas');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('La cita '+$scope.cita.vc_nombre+', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else {
            ModelService.update($scope.cita)
                .success(function (res) {
                    $message.success(res.texto);
                    $loading.hide();
                    $state.go('citas');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('La cita '+$scope.cita.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){

        // Definir Modelo
        ModelService.addModel('citas');

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
                            $scope.cita               = res;
                            $scope.cita.id_agenda     = String($scope.cita.id_agenda);
                            var dt_fecha              = res.dt_fecha.date;
                            $scope.cita.dt_fecha      = new Date(dt_fecha);
                            $scope.cita.tm_cita       = new Date(dt_fecha);
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
