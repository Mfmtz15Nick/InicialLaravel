/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Fabricas de Utilidades
|
*/

+function(){

    angular.module('gl.util.factories',[])

        .factory('$util', [ '$state', '$stateParams',
            function($state, $stateParams){

            var factory = {
                getPosition : function ( obj, key, val ){
                    var i;
                    for( i in obj){
                        if(obj[i][key] == val){
                            return i;
                            break;
                        }
                    }
                },
                getPositionInner : function ( obj, key, val, inner ){
                    var i;
                    for( i in obj){
                        if(obj[i][inner][key] == val){
                            return i;
                            break;
                        }
                    }
                },
                getPositionJson : function( obj, json ){
                    for(i in obj){
                        if( JSON.stringify( obj[i] ) === JSON.stringify( json ) ){
                            return i;
                            break;
                        }
                    }
                },
                stateParams : function( callbackEdit, callbackNew ){
                    if ( $stateParams.id )
                    {
                        if( !isNaN($stateParams.id) ) {

                            callbackEdit();

                        } else {
                            $state.go("home");
                        }
                    } else if ( $state.current.name.search("Edit") != -1 ) {
                        $state.go("home");
                    }
                    else{
                        if( !!callbackNew ) callbackNew();
                    }
                }
            };

            return factory;
        }])

        .factory('$message', [ function(){
            var types = {
                warning : 'warning',
                error   : 'error',
                info    : 'info',
                success : 'success'
            };
            var defaults = {
                title       : 'Notificación',
                text        : 'mensaje',
                type        : types.info
            };

            //Funcion que genera el mensaje
            var message = function(){
                if (arguments[0] === undefined) {
                    alert('Message espera por lo menos un parametro!');
                    return false;
                }

                var params = angular.extend({}, defaults);

                //Validamos que tipo es el parametro enviado
                switch (typeof arguments[0]) {
                    case 'string':
                        if( arguments.length > 1 ){
                            params.title = arguments[0] || '';
                            params.text = arguments[1] || '';
                        }
                        else
                            params.text = arguments[0] || '';
                        break;

                    case 'object':
                        $.extend( params, arguments[0]);
                        break;

                    default:
                        alert('Message espera los parametros de tipo "string" u "object"');
                        return false;
                }

                toastr.options = $.extend({},{
                    closeButton     : true,
                    progressBar     : true,
                    showMethod      : 'slideDown',
                    positionClass   : 'toast-bottom-right',
                    timeOut         : 5000,
                    extendedTimeOut : 1000,
                    newestOnTop     : true
                }, params);

                if( params.confirm )
                {
                    if( params.title == defaults.title )
                        params.title = "Confirmación";

                    swal({  title: params.title,
                            text: params.text,
                            type: "info",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar",
                            closeOnConfirm: false,
                            closeOnCancel: true,
                            html:true
                        },
                        function(isConfirm){
                            if( typeof params.callback != 'undefined' && isConfirm)
                                params.callback( swal );

                        });
                }
                else
                    toastr[ params.type ](params.text, params.title);

            };

            //Tipos de alertas
            var _default = function(){
                var params = angular.extend( defaults, {type  : types.default} );

                switch (typeof arguments[0]) {
                    case 'string':
                        if( arguments.length > 1 ){
                            params.title = arguments[0] || '';
                            params.text = arguments[1] || '';
                        }
                        else
                            params.text = arguments[0] || '';
                        break;

                    case 'object':
                        angular.extend(params, arguments[0] || {} );
                        break;
                }

                message(params);
            };

            var _info = function(){
                var params = angular.extend( defaults, {type  : types.info} );

                switch (typeof arguments[0]) {
                    case 'string':
                        if( arguments.length > 1 ){
                            params.title = arguments[0] || '';
                            params.text = arguments[1] || '';
                        }
                        else
                            params.text = arguments[0] || '';
                        break;

                    case 'object':
                        angular.extend(params, arguments[0] || {} );
                        break;
                }

                message(params);
            };

            var _success = function(){
                var params = angular.extend( defaults, {type  : types.success} );

                switch (typeof arguments[0]) {
                    case 'string':
                        if( arguments.length > 1 ){
                            params.title = arguments[0] || '';
                            params.text = arguments[1] || '';
                        }
                        else
                            params.text = arguments[0] || '';
                        break;

                    case 'object':
                        angular.extend(params, arguments[0] || {} );
                        break;
                }

                message(params);
            };
            var _error = function(){
                var params = angular.extend( {}, defaults,{type  : types.error} );

                switch (typeof arguments[0]) {
                    case 'string':
                        if( arguments.length > 1 ){
                            params.title = arguments[0] || '';
                            params.text = arguments[1] || '';
                        }
                        else
                            params.text = arguments[0] || '';
                        break;

                    case 'object':
                        angular.extend(params, arguments[0] || {} );
                        break;
                }

                message(params);
            };
            var _warning = function(){
                var params = angular.extend( {}, defaults, {type  : types.warning } );

                switch (typeof arguments[0]) {
                    case 'string':
                        if( arguments.length > 1 ){
                            params.title = arguments[0] || '';
                            params.text = arguments[1] || '';
                        }
                        else
                            params.text = arguments[0] || '';
                        break;

                    case 'object':
                        angular.extend(params, arguments[0] || {} );
                        break;
                }

                message(params);
            };

            var _confirm = function(){
                var params = angular.extend( {}, defaults, {type  : types.info, confirm : true} );

                switch (typeof arguments[0]) {
                    case 'string':
                        if( arguments.length > 1 ){

                            params.title = arguments[0] || '';
                            params.text = arguments[1] || '';
                        }
                        else
                            params.text = arguments[0] || '';
                        break;

                    case 'object':
                        angular.extend(params, arguments[0] || {} );
                        break;
                }

                message(params);
            };

            return {
                default : _default,
                info    : _info,
                success : _success,
                error   : _error,
                warning : _warning,
                confirm : _confirm
            }
        }])

        .factory('$loading', [function(){
            return{
                status : {
                    value : false
                },
                show : function(){
                    this.status.value = true;
                },
                hide : function(){
                    this.status.value = false;
                }
            }
        }])
}();
