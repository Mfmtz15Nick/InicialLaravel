<?php
	//Enviar Correo //////////////////////////////////////////////////////
	require('class.phpmailer.php');

	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->Host = "localhost";
	$mail->From = 'no-reply@geeklopers.com';
	$mail->FromName = 'Gaviana';
	$mail->Subject = "Gaviana | ".$_GET['vc_titulo'];

	//Enviar
	$mail->AddAddress($_GET['vc_email'], $_GET['vc_nombre']);

	$body  = "
    	<body style='text-align: center; color: #fff; font-size: 14px; font-weight: 300; background-color: #f5f5f5; padding: 50px 0;'>
    		<section>
    			<div class='view sesion cliente cliente-login' style='color: #000000; background-color: #FFFFFF; border: 3px solid black; padding: 20px 20px 10px 20px;'>
    				<div class='section1'>
    					<div class='container'>
    						<div class='row'>
    						</div>
    						<div class='row' style='text-align: left'>
    							<div class='col s12'>
    								<div class='contenedor contenedor1'>
    									<div class='row'>
    										DESCRPCION DEL CORREO
    									</div>
    								</div>
    							</div>
    						</div>
    					</div>
    				</div>
    			</div>
    		</section>
    	</body>
	";

	//echo $body;
	$mail->Body = $body;
	$mail->IsHTML(true);
	$mail->Send() or die ('<div class="alert alert-danger">Se presentó un problema al enviar el mensaje, intentelo nuevamente.</div><script>jQuery(".alert").fadeOut(5000);</script>');
	    echo'
		<div class="alert alert-success" style="margin-top: 40px;">'.$_GET['vc_mensaje'].'</div>
		<script type="text/javascript">
			jQuery("#limpiar").click();

		</script>
	';

?>
