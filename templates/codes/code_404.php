<?php
	include('templates/Template_Header.php');
	$message = ($message == null) ? "Veuillez nous excuser mais la page que vous demandez n'existe pas encore." : $message;
?>
            <div style="text-align: center">
                <h1 class="error-number">404</h1>
                <h5><?php show($message); ?></h5>
                <p><a href="#" onclick="history.back()">Retour</a></p>
            </div>
<?php
	include('templates/Template_Footer.php');
?>