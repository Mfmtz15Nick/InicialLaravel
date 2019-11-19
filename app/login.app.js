/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| Sergio Carreon
| - Controllador Login de la Aplicación
|
*/

+function(){

var stateProvider = null,
    urlRouterProvider = null;

var login = angular.module(
    'login',
    [
        'gl.util.factories',
        'gl.util.services',
        'gl.validate.service',
        'gl.authentication.services'
    ]
);

login.config(['$httpProvider', function($httpProvider){
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

/* CONTROLLER */
login.controller( 'loginController', ['$scope', '$rootScope', '$location', '$message', '$loading', '$validate', '$authentication',
    function( $scope, $rootScope, $location, $message, $loading, $validate, $authentication ){

    // Scope Variables
    $scope.usuario = { vc_email: 'admin@bladmir.com', vc_password: 'Admin123.'};
    
    // Scope Functions
    $scope.login = function(){

        if( !$validate.form('form-validate') )
            return;

        $authentication.login( $scope.usuario, function( res ){

            if( res.estatus ){
                localStorage.setItem('gc.token', res.token);
                window.location.href = "admin";

            } else {
                if( res.texto ){
                    $message.warning( res.texto );
                } else {
                    $message.warning("No se pudo realizar el inicio de sesión.");
                }
            }
        });
    };

}]);

}();
