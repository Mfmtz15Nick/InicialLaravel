/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de clientes
|
*/

var app = angular.module('clientes', []);

// Controller
app.controller( 'clientesController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $http  ){

// Scope Variables
    $scope.clientes               = [];
    $scope.clientesPaginados      = [];
    $scope.cargando               = false;
    $scope.consultor              = false;
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.clientesPaginados      = [];
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.cargando               = false;
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.SISTEMA                = 1;
    $scope.buscarRol              = '';
    $scope.roles                  = [];

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
                $scope.clientes     = res.clientes.data;
                $scope.anteriorUrl  = res.clientes.prev_page_url;
                $scope.siguienteUrl = res.clientes.next_page_url;
                $scope.paginaActual = res.clientes.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los clientes.");
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

        urlConsulta = 'api/clientes?page='+numero;

        if( ( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ) && $scope.buscarRol  ){
          urlConsulta = 'api/clientes/'+$scope.buscar+'/'+$scope.buscarRol+'/buscar?page='+numero;
        }
        else if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/clientes/'+$scope.buscar+'/buscar?page='+numero;
        }
        else if ($scope.buscarRol) {
          urlConsulta = 'api/clientes/'+$scope.buscarRol+'/buscarPorRol?page='+numero;
        }

          ModelService.custom('get', urlConsulta)
            .success(function(res){
                $scope.clientes               = res.clientes.data;
                $scope.clientesPaginados      = res.clientes.data;
                $scope.anteriorUrl            = res.clientes.prev_page_url;
                $scope.siguienteUrl           = res.clientes.next_page_url;
                $scope.paginaActual           = res.clientes.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los clientes.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
      }
    };

    $scope.buscarclientes = function(){
      if( ( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ) || $scope.buscarRol ){

        if( ( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ) && $scope.buscarRol  ){
          urlConsulta = 'api/clientes/'+$scope.buscar+'/'+$scope.buscarRol+'/buscar';
        }
        else if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/clientes/'+$scope.buscar+'/buscar';
        }
        else if ($scope.buscarRol) {
          $loading.show();
          urlConsulta = 'api/clientes/'+$scope.buscarRol+'/buscarPorRol';
        }

        ModelService.custom('get', urlConsulta)
          .success(function(res){
            console.log(res)

              $scope.clientes           = res.clientes.data;
              $scope.clientesPaginados  = res.clientes.data;
              $scope.total              = res.clientes.last_page;
              $scope.anteriorUrl        = res.clientes.prev_page_url;
              $scope.siguienteUrl       = res.clientes.next_page_url;
              $scope.paginaActual       = res.clientes.current_page;
          })
          .error(function (error) {
              if(error.texto){
                  $message.warning(error.texto);
              } else {
                  $message.warning("No se pudieron obtener los clientes.");
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
        $state.go('clientesNuevo');
    };

    $scope.editar = function( cliente ) {
        $state.go('clientesEditar', { id : cliente.id });
    };

    $scope.eventos = function( cliente ) {
        console.log(cliente.id)
        $state.go('clientesEventos', { id : cliente.id });
    };

    $scope.eliminar = function( cliente ) {
        console.log(cliente)
        $message.confirm({
            text    : '¿Estás seguro de eliminar el clientes '+cliente.vc_nombre+' '+cliente.vc_apellido+'?',
            callback : function( msg ){
                $loading.show();
                ModelService.delete( cliente.id_cliente )
                    .success(function(res){
                        msg.close();
                        var posicion = $util.getPosition($scope.clientes, 'id', cliente.id);
                        $scope.clientes.splice( posicion, 1 );
                        $message.success(res.texto);
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('El clientes '+cliente.vc_nombre+' '+cliente.vc_apellido+', no pudo eliminar correctamente.');
                        }
                    })
                    .finally(function(){
                        $loading.hide();
                    });
            }
        });
    };

    $scope.init = function() {

        ModelService.addModel('clientes');

        $scope.cargando   = true;
        

        ModelService.list()
            .success(function( res ){
                $rol = $scope.usuario.rol.id;
                if($rol == 4)
                    $scope.consultor = true;

                $scope.clientes           = res;
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
