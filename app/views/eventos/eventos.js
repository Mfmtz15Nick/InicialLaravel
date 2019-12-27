/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de Eventos
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
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.eventosPaginados      = [];
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.cargando               = false;
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.SISTEMA                = 1;

    var urlConsulta;

// Scope Funciones

    $scope.paginador = function(numero) {
        return new Array(numero);
    }

    $scope.cambiarPagina = function(url){
        $scope.cargando = true;
        $loading.show();
        ModelService.custom('get', url )
            .success(function(res){
                $scope.eventos     = res.data;
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

        urlConsulta = 'api/eventos?page='+numero;

        if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/eventos/'+$scope.buscar+'/buscar?page='+numero;
        }

          ModelService.custom('get', urlConsulta)
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
    };

    $scope.buscareventos = function(){
      if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda  ){

        urlConsulta = 'api/eventos/'+$scope.buscar+'/buscar';

        ModelService.custom('get', urlConsulta)
          .success(function(res){
              $scope.eventos              = res.data;
              $scope.eventosPaginados     = res.data;
              $scope.total              = res.last_page;
              $scope.anteriorUrl        = res.prev_page_url;
              $scope.siguienteUrl       = res.next_page_url;
              $scope.paginaActual       = res.current_page;
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
          });
      }
      else {
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
            text    : '¿Estás seguro de eliminar la evento '+evento.vc_nombre+'?',
            callback : function( msg ){
                $loading.show();
                ModelService.delete( evento.id )
                    .success(function(res){
                        msg.close();
                        var posicion = $util.getPosition($scope.eventos, 'id', evento.id);
                        $scope.eventos.splice( posicion, 1 );
                        $message.success(res.texto);
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('La evento '+evento.vc_nombre+', no se pudo eliminar correctamente.');
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

        $scope.cargando   = true;

        ModelService.list()
            .success(function( res ){
                $scope.eventos              = res.data;
                $scope.eventosPaginados     = res.data;
                $scope.total              = res.last_page;
                $scope.anteriorUrl        = res.prev_page_url;
                $scope.siguienteUrl       = res.next_page_url;
                $scope.paginaActual       = res.current_page;
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
