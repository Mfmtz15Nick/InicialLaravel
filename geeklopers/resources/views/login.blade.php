<!DOCTYPE html>
<html ng-app="login">
	<head lang="es">

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

        <meta name="author" content="Forwxrd">
        <meta name="description" content=""/>
        <meta name="keywords" content=""/>
        <meta name="webmaster" content="Geeklopers">

        <link rel="shortcut icon" href="{{ asset('images/icon.ico') }}">
		<title>Geeklpers :: Login</title>

		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/themify-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/toastr/toastr.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">
	</head>
	<body ng-controller="loginController" class="view login">

		<section>
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-12">
						<div class="content text-center">
							<div class="companies">
								<img src="{{ asset('images/logo-blanco.png') }}" alt="Geeklopers - logo" style="width: 75%">
							</div>
							<form ng-submit="login()" class="form-validate">
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<input type="email" ng-model="usuario.vc_email" class="form-control required" placeholder="Correo Electrónico">
										</div>
										<div class="form-group">
											<input type="password" ng-model="usuario.vc_password" class="form-control required" placeholder="Contraseña">
										</div>
										<div class="form-group">
											<button type="submit" class="btn">Iniciar sesión</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>

		<footer>
			<div class="container">
				<div class="row">
					<div class="col-sm-6 foot text-left">
						® 2019 Gestor
					</div>
					<div class="col-sm-6 foot text-right">
						Developed by <a href="http://geeklopers.com" target="_blank">Geeklopers</a>
					</div>
				</div>
			</div>
		</footer>

		<!-- JS -->
		<script type="text/javascript" src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/angular/angular.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('bower_components/toastr/toastr.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/factories/util.factories.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/services/util.services.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/services/validate.services.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/utils/services/authentication.services.js') }}"></script>
		<script type="text/javascript" src="{{ asset('app/login.app.js') }}"></script>
	</body>
</html>
