<?php //include_once("funciones_comunes.php");?>
<div id="content" style="width:900px;margin-left:auto;margin-right:auto;">
	<div id="col_izq">
<form name="formProv" action="mostrarBDrecetas.php?tipo=provincia" method="post">
		<select id="selectprov" name="selectprov" onchange="this.form.submit()" style="HEIGHT: 25px; LIST-STYLE: square;  WIDTH: 120px">
		<option value="">--Elija provincia--</option>
		<option value="HUELVA">HUELVA</option>
		<option value="SEVILLA">SEVILLA</option>
		<option value="CORDOBA">CORDOBA</option>
		<option value="JAEN">JAEN</option>
		<option value="CADIZ">CADIZ</option>
		<option value="MALAGA">MALAGA</option>
		<option value="GRANADA">GRANADA</option>
		<option value="ALMERIA">ALMERIA</option>
		</select>
</form>
<br/>
<h3> CONSULTAR POR DIETA:</h3>
<!--<div style="text-align:left;font-size:12px;margin-left:5px;">-->

<ul style="list-style:none;margin-left:10px;padding:0px;text-align:left;">
	<li><a href="mostrarBDrecetas.php?tipo=vegetariana">VEGETARIANA</a><br/></li>
	<li><a href="mostrarBDrecetas.php?tipo=vegana">VEGANA</a><br/></li>
	<li><a href="mostrarBDrecetas.php?tipo=celiaco">CELIACO</a><br/></li>
	<li><a href="mostrarBDrecetas.php?tipo=light">LIGHT</a><br/><br/></li>
</ul>

<h3> RESTAURANTES COLABORADORES:</h3>
<form name="formEsp" action="mostrarBDrecetas.php?tipo=especial" method="post">
	<select id="selectprov2" name="selectprov2" style="HEIGHT: 25px; LIST-STYLE: square;  WIDTH: 135px">
	<option value="HUELVA">HUELVA</option>
	<option value="SEVILLA">SEVILLA</option>
	<option value="CORDOBA">CORDOBA</option>
	<option value="JAEN">JAEN</option>
	<option value="CADIZ">CADIZ</option>
	<option value="MALAGA">MALAGA</option>
	<option value="GRANADA">GRANADA</option>
	<option value="ALMERIA">ALMERIA</option>
	</select><br/><br/>
	<!--onchange="this.form.submit()"-->
	<select id="selectesp" name="selectesp"  style="HEIGHT: 25px; LIST-STYLE: square;  WIDTH: 135px">
		<option value="PESCADO">PESCADO</option>
		<option value="MARISCO">MARISCO</option>
		<option value="CARNE">CARNE</option>
		<option value="ARROZ">ARROZ</option>
	</select>
<input name="enviar" id="enviar" type="submit" class="boton" value="BUSCAR!" style="font-size: 14px; font-family: Arial;">
</form><br/>
<h3> ULTIMAS 5 RECETAS!:</h3>
<?php
//Consulta select para obtener las últimas 5 recetas subidas por usuarios
	try{
        $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
		
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM receta ORDER BY fecha DESC LIMIT 5";

        $st = $db->prepare($query);

        $st->execute();

        $recetas = $st->fetchAll();

    } catch (PDOException $e) {
        echo '¡Error!: ',$e->getMessage(),'<br />';
        die();
    } catch (Throwable $e) {
        echo '¡Error!: ',$e->getMessage(),'<br />';
        die();
	}
	
	if(isset($recetas)) :
?>
	<!-- Creo un formulario por cada receta -->
	<!-- El formulario contiene un campo oculto que contiene el id de la receta y un hyperlink para redirigir al detalle de la receta -->
		<ul>
			<?php $counter = 1 ?>
			<?php foreach($recetas as $receta) : ?>
				<li>
					<form id="<?php echo 'formRecipe' . $counter; ?>" action="mostrarBDrecetas.php?tipo=receta" method="POST">
						<input type="hidden" name="recipe" value="<?php echo $receta['id_receta']; ?>">
						<a style="cursor:hand;" onClick="document.getElementById(<?php echo "'" . "formRecipe" . $counter . "'"; ?>).submit();"><?php echo $receta['titulo']; ?></a>
					</form>
				</li>
				<?php $counter++; ?>
			<?php endforeach; ?>
		</ul>
<?php	endif; ?>
</div>
