/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de Usuarios
|
*/

var app = angular.module('usuarios', []);

// Controller
app.controller( 'usuariosController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $http  ){

// Scope Variables
    $scope.usuarios               = [];
    $scope.usuariosPaginados      = [];
    $scope.cargando               = false;
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.usuariosPaginados      = [];
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
                $scope.usuarios     = res.usuarios.data;
                $scope.anteriorUrl  = res.usuarios.prev_page_url;
                $scope.siguienteUrl = res.usuarios.next_page_url;
                $scope.paginaActual = res.usuarios.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los usuarios.");
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

        urlConsulta = 'api/usuarios?page='+numero;

        if( ( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ) && $scope.buscarRol  ){
          urlConsulta = 'api/usuarios/'+$scope.buscar+'/'+$scope.buscarRol+'/buscar?page='+numero;
        }
        else if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/usuarios/'+$scope.buscar+'/buscar?page='+numero;
        }
        else if ($scope.buscarRol) {
          urlConsulta = 'api/usuarios/'+$scope.buscarRol+'/buscarPorRol?page='+numero;
        }

          ModelService.custom('get', urlConsulta)
            .success(function(res){
                $scope.usuarios               = res.usuarios.data;
                $scope.usuariosPaginados      = res.usuarios.data;
                $scope.anteriorUrl            = res.usuarios.prev_page_url;
                $scope.siguienteUrl           = res.usuarios.next_page_url;
                $scope.paginaActual           = res.usuarios.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los usuarios.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
      }
    };

    $scope.buscarUsuarios = function(){
      if( ( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ) || $scope.buscarRol ){

        if( ( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ) && $scope.buscarRol  ){
          urlConsulta = 'api/usuarios/'+$scope.buscar+'/'+$scope.buscarRol+'/buscar';
        }
        else if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/usuarios/'+$scope.buscar+'/buscar';
        }
        else if ($scope.buscarRol) {
          $loading.show();
          urlConsulta = 'api/usuarios/'+$scope.buscarRol+'/buscarPorRol';
        }

        ModelService.custom('get', urlConsulta)
          .success(function(res){
              $scope.usuarios           = res.usuarios.data;
              $scope.usuariosPaginados  = res.usuarios.data;
              $scope.total              = res.usuarios.last_page;
              $scope.anteriorUrl        = res.usuarios.prev_page_url;
              $scope.siguienteUrl       = res.usuarios.next_page_url;
              $scope.paginaActual       = res.usuarios.current_page;
          })
          .error(function (error) {
              if(error.texto){
                  $message.warning(error.texto);
              } else {
                  $message.warning("No se pudieron obtener los usuarios.");
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
        $state.go('usuariosNuevo');
    };

    $scope.editar = function( usuario ) {
        $state.go('usuariosEditar', { id : usuario.id });
    };

    $scope.eliminar = function( usuario ) {
        $message.confirm({
            text    : '¿Estás seguro de eliminar el usuarios '+usuario.vc_nombre+' '+usuario.vc_apellido+'?',
            callback : function( msg ){
                $loading.show();
                ModelService.delete( usuario.id )
                    .success(function(res){
                        msg.close();
                        var posicion = $util.getPosition($scope.usuarios, 'id', usuario.id);
                        $scope.usuarios.splice( posicion, 1 );
                        $message.success(res.texto);
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('El usuarios '+usuario.vc_nombre+' '+usuario.vc_apellido+', no pudo eliminar correctamente.');
                        }
                    })
                    .finally(function(){
                        $loading.hide();
                    });
            }
        });
    };

    $scope.init = function() {

        ModelService.addModel('usuarios');

        $scope.cargando   = true;
        $scope.buscarRol  = '';
        $scope.buscar     = "";

        ModelService.list()
            .success(function( res ){
                $scope.roles              = res.roles;
                $scope.usuarios           = res.usuarios.data;
                $scope.usuariosPaginados  = res.usuarios.data;
                $scope.total              = res.usuarios.last_page;
                $scope.anteriorUrl        = res.usuarios.prev_page_url;
                $scope.siguienteUrl       = res.usuarios.next_page_url;
                $scope.paginaActual       = res.usuarios.current_page;
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
