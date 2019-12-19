/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Fabricas de Menu
|
*/

+function(){
    angular.module('gl.menu.factories',[])
        .factory('$menu', [ function(){
            var factory = {
                general : [
                    // INICIO
                        { state: 'inicio', url: 'inicio', file: 'inicio', ext: 'html' },
                    // PERFIL
                        { state: 'perfil', url: 'perfil', file: 'perfil', ext: 'html' },
                    // CONFIGURACION
                        { state: 'configuracion', url: 'configuracion', file: 'configuracion', ext: 'html' },
                ],
                admin : {
                    states: [
                        // USUARIOS
                            { state: 'usuarios', url: 'usuarios', file: 'usuarios', ext: 'html' },
                            { state: 'usuariosNuevo', url: 'usuarios/nuevo', file: 'usuariosNuevo', ext: 'html' },
                            { state: 'usuariosEditar', url: 'usuarios/:id/editar', file: 'usuariosNuevo', ext: 'html' },

                        // AGENDAS
                            { state: 'agendas', url: 'agendas', file: 'agendas', ext: 'html' },
                            { state: 'agendasNuevo', url: 'agendas/nuevo', file: 'agendasNuevo', ext: 'html' },
                            { state: 'agendasEditar', url: 'agendas/:id/editar', file: 'agendasNuevo', ext: 'html' },
                            { state: 'agendasGaleria', url: 'agendas/:id/galeria', file: 'agendasGaleria', ext: 'html' },
                        // AGENDAS HORARIOS
                            { state: 'agendasHorarios', url: 'agendas/:id/horarios', file: 'agendasHorarios', ext: 'html' },
                            { state: 'agendasHorariosNuevo', url: 'agendas/:id/horarios/nuevo', file: 'agendasHorariosNuevo', ext: 'html' },
                            { state: 'agendasHorariosEditar', url: 'agendas/:id/horarios/:idDia/editar', file: 'agendasHorariosNuevo', ext: 'html' },
                      // CITAS
                          { state: 'citas', url: 'citas', file: 'citas', ext: 'html' },
                          { state: 'citasNuevo', url: 'citas/nuevo', file: 'citasNuevo', ext: 'html' },
                          { state: 'citasEditar', url: 'citas/:id/editar', file: 'citasNuevo', ext: 'html' },
                    ],
                    navigation : {
                        aside: [
                            // MENU
                            { name: 'Menú', url: '', icon: '', title: 1 },
                                { name: 'Usuarios', url: '#/usuarios', icon: 'ti-user', title: 0 },
                                { name: 'Agendas', url: '#/agendas', icon: 'ti-calendar', title: 0 },
                                { name: 'Citas', url: '#/citas', icon: 'ti-star', title: 0 },
                        ],
                        header: [
                            { name: 'Mi perfil', url: '#/perfil', icon: 'ti-user', title: 1 },
                        ],
                    },
                }
            };

            return factory;
        }])
}();
