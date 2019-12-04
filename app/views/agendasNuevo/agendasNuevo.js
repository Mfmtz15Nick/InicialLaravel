/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de agendasNuevo
|
*/

var app = angular.module('agendasNuevo', []);

// Controller
app.controller( 'agendasNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.agenda  = { vc_nombre: "" };
    $scope.flags    = { editar: false };

// Scope Functions
    $scope.regresar = function() {
        $state.go('agendas');
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        $loading.show();

        if( !$scope.flags.editar ) {
            ModelService.add($scope.agenda)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('agendas');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El agenda '+$scope.agenda.vc_nombre+', no se pudo agregar correctamente.');
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

            $scope.flags.editar = true;

            console.log($stateParams);

            ModelService.edit( $stateParams.id )
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
