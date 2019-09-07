<!DOCTYPE html>
<html ng-app="admin" class="nocturno1">
	<head lang="es">

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

        <meta name="author" content="Forwxrd">
        <meta name="description" content=""/>
        <meta name="keywords" content=""/>
        <meta name="webmaster" content="Geeklopers">

        <link rel="shortcut icon" href="{{ asset('images/icon.ico') }}">
		<title>Botánico :: Administrador</title>

		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/themify-icons/css/themify-icons.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/toastr/toastr.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/bootstrap-sweetalert/dist/sweetalert.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/angular-bootstrap-colorpicker/css/colorpicker.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/admin.css') }}">

	</head>
	<body ng-controller="adminController" class="centro" ng-class="bodyClass">

		<div class="lock" ng-show="lock.value">
            <div class="sk-spinner sk-spinner-wave">
                <div class="sk-rect1"></div>
                <div class="sk-rect2"></div>
                <div class="sk-rect3"></div>
                <div class="sk-rect4"></div>
                <div class="sk-rect5"></div>
                <h4>Cargando</h4>
            </div>
        </div>

    	<aside>
        	<div class="header">
        		<a href="#">
        			<div class="icono">
        				<img src="{{ asset('images/logo_verde.png') }}" alt="Botánico - logo">
        			</div>
        		</a>
        	</div>
        	<div class="section menuizquierdo">
        		<ul>
                    <li class="title text-center" style="margin-top: -10px;">[[ login.vc_nombre ]] [[ login.vc_apellido ]]</li>
        			<li ng-repeat="menu in menu.aside" ng-class="menu.title == 1 ? 'title' : ''">
						<a href="[[menu.url]]" ng-if="!menu.title">
							<i class="[[menu.icon]]"></i> <span>[[ menu.name ]]</span>
						</a>
						<span ng-if="menu.title">[[ menu.name ]]</span>
					</li>
        		</ul>
        	</div>
    	</aside>

        <header>
        	<div class="buttons">
                <div class="row">
                    <div class="col-xs-12">
                        <ul>
                            <li><a ng-click="logout()"><i class="ti-close"></i></a></li>
                        </ul>
                    </div>
                </div>
        	</div>
        </header>

        <section>
        	<div class="container">
        		<div class="row">
        			<div class="col-xs-12">
								<div class="area" ui-view></div>
        			</div>
        		</div>
        	</div>
        </section>

		<!-- JS -->
		<script type="text/javascript" src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/toastr/toastr.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/bootstrap-sweetalert/dist/sweetalert.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular/angular.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular-i18n/angular-locale_es-mx.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular-ui-router/release/angular-ui-router.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/ocLazyLoad/dist/ocLazyLoad.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/moment/moment.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/chart.js/dist/Chart.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/ng-sortable/dist/ng-sortable.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular-file-upload/dist/angular-file-upload.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/tinymce/tinymce.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular-ui-tinymce/src/tinymce.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/factories/menu.factories.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/factories/util.factories.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/factories/interceptor.factories.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/services/util.services.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/services/validate.services.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/services/authentication.services.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/pagination.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/admin.app.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.min.js')}}"></script>
	</body>
</html>
