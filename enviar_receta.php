<?php 
//include_once("funciones_comunes.php");
include_once("cabecera.php");
include_once("menu.php");
include_once("col_izq.php"); 


try{
	$db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$query = "SELECT nombre, id_ingred FROM ingrediente";

	$st = $db->prepare($query);

	$st->execute();

	$ingredientes = $st->fetchAll(PDO::FETCH_ASSOC);

	$query = "SELECT tipos_receta, id_tipo_receta FROM tipo_receta";

	$st = $db->prepare($query);

	$st->execute();

	$tiposReceta = $st->fetchAll(PDO::FETCH_ASSOC);

	$query = "SELECT nombre, id_provincia FROM provincia";

	$st = $db->prepare($query);

	$st->execute();

	$provincias = $st->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	echo '¡Error!: ',$e->getMessage(),'<br />';
	die();
} catch (Throwable $e) {
	echo '¡Error!: ',$e->getMessage(),'<br />';
	die();
}


?>
<script>
function add_ingrediente (obj)
{	
	num = parseInt (obj.value)
	objTxt = eval ("document.datos.prod_" + num)	
	// si el campo producto es vacio, el boton no hace nada
	if (objTxt.value == "")
		return
		
	num ++	
	obj.value = num
	oTR = document.createElement ("TR")
	// Creo el input
	oTD = document.createElement ("TD")	
	oInput = document.createElement ("INPUT")	
	oInput.type = "number"	
	oInput.name = "cant_" + num
	oInput.id = "cant_" + num
	oInput.className = "input2"
	oInput.style.width = "50px"
	oInput.value = ""
	oTD.height = "25"		
	oTD.appendChild (oInput)		
	oTR.appendChild (oTD)
	
	
	//td en blanco
	oTD2 = document.createElement ("TD")
	oTR.appendChild (oTD2)
	
	// Creo el input
	oTDsel = document.createElement ("TD")	
	oSel = document.createElement ("SELECT")
	oSel.name = "medi_" + num
	oSel.id = "medi_" + num
	oSel.className = "input2"
		
	oTDsel.appendChild (oSel)	
	oTR.appendChild (oTDsel)

	//td en blanco
	oTD3 = document.createElement ("TD")
	oTR.appendChild (oTD3)
	
	// Creo el otro input
	oTD4 = document.createElement ("TD")	
	oInput = document.createElement ("SELECT")
	oInput.id = "prod_" + num
	oInput.name = "prod_" + num
	oInput.style.width = "370px"

	<?php

	if(isset($ingredientes)){
		foreach($ingredientes as $ingrediente): ?>
		
		oInput.innerHTML += "<option value='<?php echo $ingrediente["id_ingred"]; ?>'><?php echo $ingrediente["nombre"]; ?></option>"

		<?php endforeach;
	}

	?>

	oTD4.appendChild (oInput)	
	oTR.appendChild (oTD4)
	
	
	document.all.tabla_ingredientes.appendChild (oTR)
	
//	alert (document.all.tabla_ingredientes.innerHTML)
	
	sel = document.all.medi_1.options
	newSel = eval ("document.all.medi_" + num)
	for (i=0;i<sel.length;i++)
	{											
		newSel[i] = new Option (sel[i].text,sel[i].value);			
	}
	if (newSel.options.length > 0)
	{
		newSel.options[0].selected = true
	}			
}

function add_ingrediente2 (obj2)
{	
	num2 = parseInt (obj2.value)
	objTxt2 = eval ("document.datos.prod2_" + num2)	
	// si el campo producto es vacio, el boton no hace nada
	if (objTxt2.value == "")
		return
		
	num2 ++	
	obj2.value = num2
	oTR = document.createElement ("TR")
	// Creo el input
	oTD = document.createElement ("TD")	
	oInput = document.createElement ("INPUT")	
	oInput.type = "number"	
	oInput.name = "cant2_" + num2
	oInput.id = "cant2_" + num2
	oInput.className = "input2"
	oInput.style.width = "50px"
	oInput.value = ""
	oTD.height = "25"		
	oTD.appendChild (oInput)		
	oTR.appendChild (oTD)
	
	
	//td en blanco
	oTD2 = document.createElement ("TD")
	oTR.appendChild (oTD2)
	
	// Creo el input
	oTDsel = document.createElement ("TD")	
	oSel = document.createElement ("SELECT")
	oSel.name = "medi2_" + num2
	oSel.id = "medi2_" + num2
	oSel.className = "input2"
		
	oTDsel.appendChild (oSel)	
	oTR.appendChild (oTDsel)

	//td en blanco
	oTD3 = document.createElement ("TD")
	oTR.appendChild (oTD3)
	
	// Creo el otro input
	oTD4 = document.createElement ("TD")	
	oInput = document.createElement ("SELECT")
	oInput.style.width = "370px"	

	<?php

	if(isset($ingredientes)){
		foreach($ingredientes as $ingrediente): ?>
		
		oInput.innerHTML += "<option value='<?php echo $ingrediente["id_ingred"]; ?>'><?php echo $ingrediente["nombre"]; ?></option>"

		<?php endforeach;
	}

	?>

	oTD4.appendChild (oInput)	
	oTR.appendChild (oTD4)
	
	
	document.all.tabla_ingredientes2.appendChild (oTR)
	
//	alert (document.all.tabla_ingredientes.innerHTML)
	
	sel = document.all.medi_1.options
	newSel = eval ("document.all.medi2_" + num2)
	for (i=0;i<sel.length;i++)
	{											
		newSel[i] = new Option (sel[i].text,sel[i].value);			
	}
	if (newSel.options.length > 0)
	{
		newSel.options[0].selected = true
	}			
}
function compReceta (obj)
{
	msg = "Debe rellenar los campos:"
	valido = true
	if (obj.titulo.value == "")
	{
		msg += "\n- Nombre de la receta."
		valido = false
	}
	
	if (obj.duracion.value == "")
	{
		msg += "\n- Tiempo de preparación."
		valido = false
	}

	var algunSeleccionado = false;

	for(let i = 0; i < obj.opciones.options.length; i++){
		let opt = obj.opciones.options[i]

		if(opt.selected){
			algunSeleccionado = true;
			break;
		}
	}

	if(!algunSeleccionado){
		msg += "\n- Tipo de receta"
		valido = false
	}

	if(obj.comensales.value == ""){
		msg += "\n- Número de comensales."
		valido = false
	}
	
	if (obj.preparacion.value == "")
	{
		msg += "\n- Modo de preparación."
		valido = false
	}
	
	if (obj.prod_1.value == "" || obj.cant_1.value == "")
	{
		alert ("Debe introducir al menos un ingrediente (producto)")
		return false
	}
	
	if (valido)
	{
		if (isNaN (parseInt (obj.duracion.value,10)))
		{
			alert ("El tiempo de preparación debe ser un número.")
			return false
		}

		if (isNaN (parseInt (obj.comensales.value,10)))
		{
			alert ("Los comensales debe ser un número.")
			return false
		}
		else
		{
			return true
		}
		
	}
	else
	{
		alert (msg)
		return false
	}
}
</script>
<div align="center" style="width:700px;overflow: hidden;">
<?php /*if(estas_autenticado()){*/?>
<table width="670" border="0" cellspacing="0" cellpadding="0" style="margin-right: auto;margin-left: auto;">
  <tbody><tr>
	<td width="590" colspan="2"><img src="imagenes/1px.gif" width="1" height="8"></td>
  </tr>
  <tr>
	<td colspan="2"><h1>ENVIA TUS RECETAS</h1></td>
  </tr>
  <tr>
	<td height="15" colspan="2"></td>
  </tr>
  <tr>
	<td colspan="2" style="padding-left:30px;">Muchas gracias por colaborar con la web.</td>
  </tr>
  <tr>
	<td colspan="2" height="15"></td>
  </tr>
  <tr>
	<td colspan="2" align="right" valign="top"><table width="670" border="0" cellpadding="0" cellspacing="1" bgcolor="#ebe0de">
		<tbody><tr>
		  <td><table width="670" border="0" cellpadding="0" cellspacing="0" bgcolor="#ebe0de">
			  <tbody><tr>
				<td width="670" align="center" valign="top"><form name="datos" method="post" enctype="multipart/form-data" action="enviar_receta_nuevo.php" onsubmit="return compReceta (this)">
					<table width="550" border="0" cellpadding="0" cellspacing="0">
					  <tbody>
					  <tr>
						<td colspan="4"><table width="550" border="0" cellspacing="0" cellpadding="0">
							<tbody><tr>
							  <td height="20" colspan="4"><strong>Título de la receta:</strong></td>
							</tr>
							<tr>
							  <td height="20" colspan="4"><input type="text" name="titulo" style="width:550px"></td>
							</tr>
							
							<tr>
							  <td width="113" height="20">Tipo de plato:</td>
							  
							  <td width="226"><select multiple name="multiple[]" id="opciones">
								  
							  		<?php  
										if(isset($tiposReceta)){
											foreach($tiposReceta as $tipoReceta) : ?>
												<option value="<?php echo $tipoReceta["id_tipo_receta"] ?>"><?php echo $tipoReceta["tipos_receta"] ?></option>
											<?php endforeach;
										}
									?>
								  
								</select></td>
							 
							</tr>
                            
							<tr>
							  <td height="20">Provincia:</td>
							  
							  <td><select name="provincia">
								  
							  		<?php  
										if(isset($provincias)){
											foreach($provincias as $provincia) : ?>
												<option value="<?php echo $provincia["id_provincia"] ?>"><?php echo $provincia["nombre"] ?></option>
											<?php endforeach;
										}
									?>
								  
								</select></td>
							</tr>
							
							<tr>
							  <td height="20">Nivel de dificultad:</td>
							  
							  <td><select name="dificultad">
								  
								  <option value="Muy fácil">Muy fácil</option>
								  
								  <option value="Fácil">Fácil</option>
								  
								  <option value="Media">Media</option>
								  
								  <option value="Difícil">Difícil</option>
								  
								  <option value="Experto">Experto</option>
								  
								</select></td>
							  <td>Calculada para:
							  <input type="text" name="comensales" size="3" maxlength="3">
								personas</td> 
                               </tr><tr></tr>
                               <tr>
                      		<td>Horno</td><td><input type="checkbox" name="horno" value="0" /></td></tr>
                            <tr><td>Batidora</td><td><input type="checkbox" name="batidora" value="0" /></td></tr>
                            <tr><td>Microondas</td><td><input type="checkbox" name="microondas" value="0" /></td></tr>
                            <tr><td>Thermomix</td><td><input type="checkbox" name="thermomix" value="0" /></td></tr>
                            <tr><td>Vegetariana</td><td><input type="checkbox" name="vegetariana" value="0" /></td></tr>
                            <tr><td>Vegana</td><td><input type="checkbox" name="vegana" value="0" /></td></tr>
                            <tr><td>Celiacos</td><td><input type="checkbox" name="celiacos" value="0" /></td></tr>
                            <tr><td>Light</td><td><input type="checkbox" name="light" value="0" /></td>
                            
                      </tr>
							
							
							<tr>
							  <td height="20">Tiempo de preparación:</td>
							  <td colspan="1"><input type="text" name="duracion" size="3" maxlength="3">
								minutos</td>
							  <td colspan="2">Introduzca foto:<input name="foto" type="file" size="35"></td>
							</tr>
						</tbody></table></td>
					  </tr>
					  
					  <tr>
						<td height="20" colspan="4"><br/><strong>Ingredientes Principales:</strong></td>
					  </tr>
					 
					  <tr>
						<td height="20" colspan="4">Debes indicar 
						  por separado la cantidad, la medida y el 
						  nombre del producto ingrediente. </td>
					  </tr>
					  
					  <tr>
						<td colspan="4" id="td_tabla"><table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tbody id="tabla_ingredientes">
							<input type="hidden" name="contador" value="1">
							<tr>
							  <td height="20" valign="bottom">Cantidad</td>
							  <td><img src="imagenes/1px.gif" width="10" height="10"></td>
							  <td valign="bottom">Medida</td>
							  <td><img src="imagenes/1px.gif" width="10" height="10"></td>
							  <td valign="bottom">Producto</td>
							</tr>
							<tr>
							  <td height="25"><input type="number" name="cant_1" style="width:50px"></td>
							  <td>&nbsp;</td>
							  <td><select name="medi_1">
								  <option value="">unidad o pieza</option>
								  <option value="gramo">gramo</option>
								</select>                                              </td>
							  <td><img src="imagenes/1px.gif" width="10" height="10"></td>
							  <td>
							  		<select style="width: 370px;" name="prod_1">
										<?php  
											if(isset($ingredientes)){
												foreach($ingredientes as $ingrediente) : ?>
													<option value="<?php echo $ingrediente["id_ingred"] ?>"><?php echo $ingrediente["nombre"] ?></option>
												<?php endforeach;
											}
										?>
									</select>
							  </td>
							</tr>
						  </tbody></table>
						  
						  </td></tr></tbody>                                        
					  
					  <tbody><tr>
						<td colspan="4"><img src="imagenes/1px.gif" width="5" height="5"></td>
					  </tr>
					  <tr>
						<td colspan="3">Una vez introducida 
						  la cantidad, medida y nombre del ingrediente, 
						  pulse en el botón "añadir 
						  ingrediente" (derecha) y aparecerá 
						  una línea vacía donde podrás 
						  seguir escribiendo.</td>
						<td align="right"><a href="javascript:add_ingrediente (document.all.contador)"><img src="imagenes/boton_anadir_ingre.gif" border="0"></a></td>
					  </tr>
					  

<tr>
						<td height="20" colspan="4"><br/><strong>Ingredientes Opcionales:</strong></td>
					  </tr>
					 
					  <tr>
						<td height="20" colspan="4">Debes indicar 
						  por separado la cantidad, la medida y el 
						  nombre del producto ingrediente. </td>
					  </tr>
<tr>
						<td colspan="4" id="td_tabla"><table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tbody id="tabla_ingredientes2">
							<input type="hidden" name="contador2" value="1">
							<tr>
							  <td height="20" valign="bottom">Cantidad</td>
							  <td><img src="imagenes/1px.gif" width="10" height="10"></td>
							  <td valign="bottom">Medida</td>
							  <td><img src="imagenes/1px.gif" width="10" height="10"></td>
							  <td valign="bottom">Producto</td>
							</tr>
							<tr>
							  <td height="25"><input type="number" name="cant2_1" style="width:50px"></td>
							  <td>&nbsp;</td>
							  <td><select name="medi2_1">
								  <option value="">unidad o pieza</option>
								  <option value="gramo">gramo</option>
								</select>                                              </td>
							  <td><img src="imagenes/1px.gif" width="10" height="10"></td>
							  <td>
							  		<select style="width: 370px;" name="prod2_1">
										<?php  
											if(isset($ingredientes)){
												foreach($ingredientes as $ingrediente) : ?>
													<option value="<?php echo $ingrediente["id_ingred"] ?>"><?php echo $ingrediente["nombre"] ?></option>
												<?php endforeach;
											}
										?>
									</select>
							  </td>
							</tr>
						  </tbody></table>
						  
</td></tr>

					  <tr>
						<td colspan="3">Una vez introducida 
						  la cantidad, medida y nombre del ingrediente, 
						  pulse en el botón "añadir 
						  ingrediente" (derecha) y aparecerá 
						  una línea vacía donde podrás 
						  seguir escribiendo.</td>
						<td align="right"><a href="javascript:add_ingrediente2 (document.all.contador2)"><img src="imagenes/boton_anadir_ingre.gif" border="0"></a></td>
					  </tr>
					  <tr>
						<td colspan="4"><img src="imagenes/1px.gif" width="5" height="15"></td>
					 </tr>  
					  
					  
					  <tr>
						<td height="20" colspan="4">Modo 
						  de preparación:</td>
					  </tr>
					  <tr>
						<td colspan="4"><textarea name="preparacion" rows="6" style="width:550px; height:100px"></textarea></td>
					  </tr>
                      
					  
					  <tr>
						<td align="left">Correo:<input type="text" name="correo" style="width:250px"></td>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						
						<td>&nbsp;</td>
					  </tr>
					  
					  <tr>
						<td colspan="4" height="20">&nbsp;</td>
					  </tr>
                       <tr>
						<td height="20" colspan="4">Observaciones:</td>
					  </tr>
					  <tr>
						<td colspan="4"><textarea name="observaciones" rows="6" style="width:550px; height:100px"></textarea></td>
					  </tr>
					  <tr>
						<td colspan="4">
						  <input align="right" type="submit" name="enviar" value="ENVIAR RECETA"></td>
					  </tr>
                    
					  <tr>
						<td colspan="4" height="20"></td>
					  </tr>
					</tbody></table>
				  </form></td>
			  </tr>
			</tbody></table></td>
		</tr>
	  </tbody></table></td>
  </tr>
</tbody></table></div>
<?php /*}else{ echo '<h1>Lo sentimos, sólo los usuarios registrados pueden enviar recetas.</h1>';}*/?>
<?php include_once("pie.php");?>