/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Servicios para Authenticación
|
*/

+function(){

    angular.module('gl.authentication.services',['gl.util.services'])

        .service('$authentication', [ '$location', 'LoginService',
			function( $location, LoginService ){

				var service = {
					usuario : {},
					view : { flag: false, usuario: {} },
					login : function( usuario, callback ){
						LoginService.login( usuario )
			                .success(function( res ){
			                	if( res.estatus ){
									service.usuario = res.usuario;
									service.view.flag = true;
									callback( res );
								} else {
									service.view.flag = false;
									callback( res );
								}
			                })
			                .error(function (error) {
								service.view.flag = false;
			                	callback( error );
			                });
					},
					logout : function( callback ){
						LoginService.logout()
							.success(function (){
								service.view.flag = false;
								callback( true );
							})
							.error(function (){
								service.view.flag = false;
								callback( false );
							});
					},
					check : function( callback ){
						LoginService.check()
							.success(function( res ){
								if( res.estatus ){
									service.usuario = res.usuario;
									service.view.flag = true;
									callback( res );
								} else {
									service.view.flag = false;
									callback( false );
								}
							})
							.error(function( error ){
								service.view.flag = false;
								callback( false );
							});
					},
					exists : function(){
			        	if ( service.usuario === null ) {
							service.view.flag = false;
			        		return false;
			        	} else {
							service.view.flag = true;
			        		return true;
			        	}
					}
				}

				return service;
	}]);
}();
