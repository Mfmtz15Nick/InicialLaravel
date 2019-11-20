/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de usuariosNuevo
|
*/

var app = angular.module('usuariosNuevo', []);

// Controller
app.controller( 'usuariosNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService ){

// Scope Variables
    $scope.usuario  = { id_genero: "", vc_nombre: "" };
    $scope.generos  = [];
    $scope.roles    = [];
    $scope.flags    = { editar: false };

// Scope Functions
    $scope.regresar = function() {
        $state.go('usuarios');
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        if ( $scope.usuario.vc_password != $scope.usuario.vc_password_re ) {
            $message.warning('Las constraseñas proporcionadas no son identicas.');
            return;
        }

        $loading.show();

        if( !$scope.flags.editar ) {
            ModelService.add($scope.usuario)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('usuarios');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El usuario '+$scope.usuario.vc_nombre+', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else {
            ModelService.update($scope.usuario)
                .success(function (res) {
                    $message.success(res.texto);
                    $loading.hide();
                    $state.go('usuarios');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El usuario '+$scope.usuario.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){

        // Definir Modelo
        ModelService.addModel('usuarios');

        $loading.show();

        ModelService.create()
            .success(function(res){

                $scope.generos  = angular.copy(res.generos);
                $scope.roles    = angular.copy(res.roles);

                // Verificar proceso Agregar o Editar
                $util.stateParams(function(){

                    $scope.flags.editar = true;

                    console.log($stateParams);

                    ModelService.edit( $stateParams.id )
                        .success(function(res){
                            $scope.usuario                = res;
                            $scope.usuario.id_genero      = String($scope.usuario.id_genero);
                            $scope.usuario.id_rol         = String($scope.usuario.id_rol);
                            $scope.usuario.vc_password_re = angular.copy($scope.usuario.vc_password);
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
