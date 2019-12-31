/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de El Vista de eventosNuevo
|
*/

var app = angular.module('eventosNuevo', []);

// Controller
app.controller('eventosNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService',
    function ($scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService) {

        // Scope Variables
        $scope.evento   = {};
        $scope.tiposEventos = [];
       
        $scope.flags      = { editar: false };
        // Scope Functions

        // var uploader = $uploader.load({
        //     url : 'api/eventos/upload',
        //     autoUpload: true,
        //     headers: {
        //         Authorization: localStorage.getItem('gjb.token')
        //     }
        // });
        //
        // $scope.uploader = uploader;
        //
        // uploader.onCompleteItem = function(item, response, status, headers){
        //
        //     $scope.evento.vc_imagenUrl = response.nombre;
        //
        //     if( uploader.queue.length > 1 ){
        //       for (var i = 0; i < uploader.queue.length; i++) {
        //         if ( i == 0 ) {
        //           uploader.queue[i].remove();
        //         }
        //       }
        //     }
        // };
        // $scope.deleteItem = function(item){
        //     $scope.evento.vc_imagenUrl = '';
        //     item.remove();
        // };
        //
        // $scope.eliminar = function() {
        //     $scope.evento.vc_imagenUrl = '';
        // };

        $scope.regresar = function () {
            $state.go('eventos');
        };

        $scope.submit = function () {
            $scope.guardar();
        };

       

        $scope.guardar = function () {

            if (!$validate.form('form-validate'))
                return;

            // if( $scope.evento.vc_imagenUrl == '' ){
            //     $message.warning('No se ha cargado ninguna imagen.');
            //     return;
            // }

            $loading.show();

            if (!$scope.flags.editar) {

                ModelService.add($scope.evento)
                    .success(function () {
                        $message.success('El evento ' + $scope.evento.vc_nombre + ', fue guardado correctamente.');
                        $state.go('eventos');
                    })
                    .error(function (error) {
                        if (error.texto) {
                            $message.warning(error.texto);
                        } else {
                            $message.warning('El evento ' + $scope.evento.vc_nombre + ', no se pudo agregar correctamente.');
                        }
                    })
                    .finally(function () {
                        $loading.hide();
                    });
            }
            else {
                ModelService.update($scope.evento)
                    .success(function () {
                        $message.success('El evento ' + $scope.evento.vc_nombre + ', fue editado correctamente.');
                        $loading.hide();
                        $state.go('eventos');
                    })
                    .error(function (error) {
                        if (error.texto) {
                            $message.warning(error.texto);
                        } else {
                            $message.warning('El evento ' + $scope.evento.vc_nombre + ', no se pudo editar correctamente.');
                        }
                    })
                    .finally(function () {
                        $loading.hide();
                    });
            }
        };

        $scope.init = function () {

            // Definir Modelo
            ModelService.addModel('eventos');

            $loading.show();


            ModelService.create()
                .success(function(res){

                  $scope.tiposEventos = angular.copy(res.tiposEventos);
                  

                    // Verificar proceso Agregar o Editar
                    $util.stateParams(function () {
                        $scope.flags.editar = true;

                        ModelService.edit($stateParams.id)
                            .success(function (res) {
                                $scope.evento = res.detalle;
                                $scope.evento.id_tiposEventos  = ''+res.detalle.id_tiposEventos;
                                
                               
                                
                                // if (res.detalle.sn_destacado == 1) $scope.evento.sn_destacado = true;
                            })
                            .error(function (error) {
                                if (error.texto) {
                                    $message.warning(error.texto);
                                } else {
                                    $message.warning("No se pudo obtener el registro.");
                                }
                            })
                            .finally(function () {
                                $loading.hide();
                            });
                        })
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
