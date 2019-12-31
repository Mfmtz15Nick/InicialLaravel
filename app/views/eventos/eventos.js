/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de eventos
|
*/

var app = angular.module('eventos', []);

// Controller
app.controller( 'eventosController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $http  ){

// Scope Variables
    $scope.eventos               = [];
    $scope.eventosPaginados      = [];
    $scope.cargando               = false;
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.minimoBusqueda         = 1;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;

    $scope.eventosPaginados  = [];

    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;

    $scope.buscar                 = "";
    $scope.cargando               = false;
    $scope.minimoBusqueda         = 1;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;

// Scope Funciones

    $scope.galeria = function( evento ) {
        $state.go('eventosGaleria', { id : evento.id });
    };

    $scope.paginador = function(numero) {
        return new Array(numero);
    }

    $scope.cambiarPagina = function(url){
        $scope.cargando = true;
        $loading.show();
        $http.get( url )
            .success(function(res){
                $scope.eventos = res.data;
                $scope.anteriorUrl  = res.prev_page_url;
                $scope.siguienteUrl = res.next_page_url;
                $scope.paginaActual = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los eventos.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
    }

    $scope.consultar = function(numero){
    // Consultar si es diferente la pagina a la actual
    if ( numero[0][0] != $scope.paginaActual ) {
        $scope.cargando = true;
        $loading.show();
        $http.get('api/eventos?page='+numero)
            .success(function(res){
                $scope.eventos               = res.data;
                $scope.eventosPaginados      = res.data;
                $scope.anteriorUrl            = res.prev_page_url;
                $scope.siguienteUrl           = res.next_page_url;
                $scope.paginaActual           = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los eventos.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
    }
    }

    $scope.buscareventos = function(){
    if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
    $http.get('api/eventos/'+$scope.buscar+'/buscar')
        .success(function(res){
            $scope.eventos          = res.data;
            $scope.eventosPaginados = res.data;
            $scope.total                 = res.last_page;
            $scope.anteriorUrl           = res.prev_page_url;
            $scope.siguienteUrl          = res.next_page_url;
            $scope.paginaActual          = res.current_page;
        })
        .error(function (error) {
            if(error.texto){
                $message.warning(error.texto);
            } else {
                $message.warning("No se pudieron obtener los eventos.");
            }
        })
        .finally(function(){
        });
    } else {
        $scope.actualizar();
    }
    }

    $scope.actualizar = function() {
        $scope.init();
    };

    $scope.agregar = function() {
        $state.go('eventosNuevo');
    };

    $scope.editar = function( evento ) {
        $state.go('eventosEditar', { id : evento.id });
    };

    $scope.eliminar = function( evento ) {
        $message.confirm({
            text    : '¿Estás seguro de eliminar el evento '+evento.detalle.vc_nombre+'?',
            callback : function( msg ){
                $loading.show();
                ModelService.delete( evento.id )
                    .success(function(){
                        msg.close();
                        var posicion = $util.getPosition($scope.eventos, 'id', evento.id);
                        $scope.eventos.splice( posicion, 1 );
                        $message.success('El evento '+evento.detalle.vc_nombre+', fue eliminado correctamente.');
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('El evento '+evento.detalle.vc_nombre+', no pudo eliminar correctamente.');
                        }
                    })
                    .finally(function(){
                        $loading.hide();
                    });
            }
        });
    };

    $scope.init = function() {

        ModelService.addModel('eventos');

        $scope.cargando = true;

        ModelService.list()
            .success(function( res ){
                $scope.eventos              = res;
                // $scope.eventosPaginados     = res.data;
                // $scope.total              = res.last_page;
                // $scope.anteriorUrl        = res.prev_page_url;
                // $scope.siguienteUrl       = res.next_page_url;
                // $scope.paginaActual       = res.current_page;
            })
            .error(function () {
                $message.warning("No se obtener los registros.");
            })
            .finally(function(){
                $scope.cargando = false;
            });
    };

// Iniciar Controller
    $scope.init();
}]);
