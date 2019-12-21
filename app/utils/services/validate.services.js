/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Servicios para Validar Formulario
|
*/

+function(){

    angular.module('gl.validate.service',[])

        .service('$validate', [ '$message',
            function( $message ) {
                this.form= function( form ){
                    var $form = $('.'+form),
                        inputs = $form.find('.required'),
                        ok = true,
                        email = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/,
                        message = 'Asegúrese de ingresar todos los campos marcados en rojo.';

                    //Recorremos los inputs
                    angular.forEach( inputs, function( input, index ){

                        var $input = angular.element( input );
                        $input.removeClass('error');

                        switch( input.type )
                        {
                            case "text":
                            case "time":
                            case "number":
                            case "password":
                            case "textarea":
                            case "file":
                            case "select-one":
                            case "date":
                            case "email":
                                if( !input.value.trim() ) { ok = false; $input.addClass('error');}
                                break;

                            case "checkbox":
                            case "radio":
                                if( !$input.is(':checked') ) {ok = false; $input.addClass('error');}
                                break;
                        }
                    });

                    //validaos si hay error.
                    if( !ok ){
                        $message.warning('Advertencia', message );
                        return false;
                    }

                    //Si todo esta bien validamos si hay un imput tipo email.
                    angular.forEach(inputs, function(input){
                        var $input = angular.element( input );

                        //Validamos si es correo.
                        if( !$input.hasClass('email') && typeof $input.attr('email') == 'undefined' && input.type != 'email' )
                            return;

                        if( !email.test( input.value ) ){
                            ok = false;
                            message = 'Escribir un correo valido.';
                            $input.addClass('error');
                        }
                    });

                    if( !ok ) $message.warning('Advertencia', message );

                    return ok;
                };

                this.clean = function( key ){
                    var $form = elements[key],
                        inputs = $form[0].getElementsByClassName('required');

                    //Recorremos los inputs
                    angular.forEach( inputs, function( input, index ){
                        var $input = angular.element( input );
                        $input.removeClass('error');
                    });
                }


            }
        ])

        .directive('required', ['$parse', function($parse) {
            return {
                restrict: 'C',
                link: function(scope, element, attrs) {

                    element.on('change', function(){
                        element.removeClass('error');
                    });
                }
            };
        }])

        .directive('numeric', function () {
            return {
                require: 'ngModel',
                restrict: 'CA',
                link: function (scope, element, attr, ngModelCtrl) {

                    element.on('keypress', function(e){
                        var charCode = (e.which) ? e.which : e.keyCode
                        return !(charCode > 31 && (charCode < 48 || charCode > 57));
                    });

                    element.on('paste',function(e){
                        e.preventDefault();
                    });
                    /*function fromUser(text) {
                        if (text) {
                            var transformedInput = text.replace(/[^0-9]/g, '');

                            if (transformedInput !== text) {
                                ngModelCtrl.$setViewValue(transformedInput);
                                ngModelCtrl.$render();
                            }
                            return transformedInput;
                        }
                        return undefined;
                    }
                    ngModelCtrl.$parsers.push(fromUser);*/
                }
            };
        })

        .directive('float',function(){
            return {
                restrict: 'CA',
                link: function (scope, element, attr) {
                    decimal = 1;
                    if( !!attr.float && !isNaN(attr.float) )
                        decimal = attr.float -1;
                    else if(!!attr.decimal && !isNaN(attr.decimal) )
                        decimal = attr.decimal -1;

                    /*element.on('keypress', function (e) {
                        var charCode = (e.which) ? e.which : e.keyCode;
                        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                            return false;
                        }
                        else {
                            //if dot sign entered more than once then don't allow to enter dot sign again. 46 is the code for dot sign
                            var parts = this.value.split('.');
                            if (parts.length > 1 && charCode == 46)
                                return false;

                            return true;
                        }
                    });

                    element.on('keyup',function(){
                       if(decimal){
                           this.value = Math.round( this.value * 100) / 100;
                       }
                    });*/

                    var fnc_decimal = function (e, el)
                    {
                        var charCode = (e.which) ? e.which : e.keyCode,
                            numero = el.value.split('.'),
                            decimales = decimal;
                            cursorPosition = function( el ){
                                if( el.createTextRange )
                                {
                                    var r = document.selection.createRange().duplicate();
                                    r.moveEnd( 'character', el.value.length );

                                    if( r.text == '' ) return el.value.length;

                                    return el.value.lastIndexOf( r.text );
                                }
                                else
                                    return el.selectionStart;
                            };

                        //validamos que sea solo numeros y el punto
                        if( charCode != 46 && charCode > 31 && ( charCode < 48 || charCode > 57 ) ) return false;

                        //validamos que solo se escriba un punto
                        if( numero.length > 1 && charCode == 46 ) return false;

                        var cursorPos = cursorPosition( el ),
                            puntoPos = el.value.indexOf('.');

                        if( cursorPos > puntoPos && puntoPos >-1 && ( numero[1].length > decimales ) ) return false;

                        return true;
                    };

                    element.on('keypress',function(e){
                       return fnc_decimal(e, this);
                    });

                    element.on('paste',function(e){
                        e.preventDefault();
                    });
                }
            };
        })

}();
