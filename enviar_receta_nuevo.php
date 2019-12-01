<?php

include_once("cabecera.php");
include_once("menu.php");
include_once("col_izq.php");
echo "<div id='enviarReceta'>";

try {
    $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    if(isset($_POST)){
        $recipe = $_POST;

        if(validation($recipe, $db)){

            try{

                $db->beginTransaction();

                $query = "INSERT INTO receta (titulo, duracion, dificultad, preparacion, horno, batidora, microondas, thermomix, 
                celiacos, light, vegetariana, vegana, validada, fecha, comensales, observaciones, numusuario_id_usuario) VALUES (:titulo, 
                :duracion, :dificultad, :preparacion, :horno, :batidora, :microondas, :thermomix, :celiacos, :light, :vegetariana, :vegana, 
                :validada, :fecha, :comensales, :observaciones, :user)";

                $st = $db->prepare($query);

                $st->bindParam(':titulo', $title);
                $st->bindParam(':duracion', $time);
                $st->bindParam(':dificultad', $difficulty);
                $st->bindParam(':preparacion', $preparation);
                $st->bindParam(':horno', $oven);
                $st->bindParam(':batidora', $beater);
                $st->bindParam(':microondas', $micro);
                $st->bindParam(':thermomix', $thermomix);
                $st->bindParam(':celiacos', $celiac);
                $st->bindParam(':light', $light);
                $st->bindParam(':vegetariana', $vegetarian);
                $st->bindParam(':vegana', $vegan);
                $st->bindParam(':validada', $valid);
                $st->bindParam(':fecha', $date);
                $st->bindParam(':comensales', $people);
                $st->bindParam(':observaciones', $comments);
                $st->bindParam(':user', $user);

                $title = $recipe["titulo"];
                $time = $recipe["duracion"];
                $difficulty = $recipe["dificultad"];
                $preparation = $recipe["preparacion"];

                if(isset($recipe["horno"])){
                    $oven = 1;
                }else{
                    $oven = 0;
                }

                if(isset($recipe["batidora"])){
                    $beater = 1;
                }else{
                    $beater = 0;
                }

                if(isset($recipe["microondas"])){
                    $micro = 1;
                }else{
                    $micro = 0;
                }

                if(isset($recipe["thermomix"])){
                    $thermomix = 1;
                }else{
                    $thermomix = 0;
                }

                if(isset($recipe["celiacos"])){
                    $celiac = 1;
                }else{
                    $celiac = 0;
                }

                if(isset($recipe["light"])){
                    $light = 1;
                }else{
                    $light = 0;
                }

                if(isset($recipe["vegetariana"])){
                    $vegetarian = 1;
                }else{
                    $vegetarian = 0;
                }

                if(isset($recipe["vegana"])){
                    $vegan = 1;
                }else{
                    $vegan = 0;
                }

                $valid = 1;
                $date = date('Y-m-d');
                $people = $recipe["comensales"];
                if(empty($recipe["observaciones"])){
                    $comments = null;
                }else{
                    $comments = $recipe["observaciones"];
                }
                $user = null;

                $st->execute();
                $recipeId = $db->lastInsertId();

                foreach($recipe["multiple"] as $type){
                    $query = "INSERT INTO receta_tipo (id_receta, id_tipo_receta) VALUES (:idReceta, :idTipo)";
                    $st = $db->prepare($query);

                    $st->bindParam(':idReceta', $idRecipe);
                    $st->bindParam(':idTipo', $idType);

                    $idRecipe = $recipeId;
                    $idType = $type;

                    $st->execute();
                }

                for($i=1; $i <= intval($recipe["contador"]); $i++){
                    if(! empty($recipe["cant_" . $i])){
                       $query = "INSERT INTO ingredientes_principales (id_ingred, id_receta, cantidad, aclaraciones) 
                       VALUES (:idIngrediente, :idReceta, :cantidad, :aclaraciones)";

                        $st = $db->prepare($query);

                        $st->bindParam(':idIngrediente', $ingredientId);
                        $st->bindParam(':idReceta', $idRecipe);
                        $st->bindParam(':cantidad', $amount);
                        $st->bindParam(':aclaraciones', $comments);

                        $ingredientId = $recipe['prod_' . $i];
                        $idRecipe = $recipeId;
                        $amount = $recipe['cant_' . $i];
                        $comments = null;

                        $st->execute();
                       
                    }
                }
            
                for($i=1; $i <= intval($recipe["contador"]); $i++){
                    if(! empty($recipe["cant2_" . $i])){
                       $query = "INSERT INTO ingredientes_opcionales (id_ingred, id_receta, cantidad, aclaraciones) 
                       VALUES (:idIngrediente, :idReceta, :cantidad, :aclaraciones)";

                        $st = $db->prepare($query);

                        $st->bindParam(':idIngrediente', $ingredientId);
                        $st->bindParam(':idReceta', $idRecipe);
                        $st->bindParam(':cantidad', $amount);
                        $st->bindParam(':aclaraciones', $comments);

                        $ingredientId = $recipe['prod2_' . $i];
                        $idRecipe = $recipeId;
                        $amount = $recipe['cant2_' . $i];
                        $comments = null;

                        $st->execute();
                       
                    }
                }

                $query = "INSERT INTO receta_provincia (receta_id_receta, provincia_id_provincia) VALUES (:idReceta, :idProvincia)";
                $st = $db->prepare($query);

                $st->bindParam(':idReceta', $idRecipe);
                $st->bindParam(':idProvincia', $idProvince);

                $idRecipe = $recipeId;
                $idProvince = $recipe["provincia"];

                $st->execute();

                if(isset($_FILES["foto"])){
                    $image = $_FILES["foto"];
                    if($image["type"] == "image/jpeg" || $image["type"] == "image/png" || $image["type"] == "image/jpg"){
                        $rute = "fotos/" . $image["name"];
                        move_uploaded_file($image["tmp_name"], $rute);
                        $size = getimagesize($rute);
                        $width = $size[0];
                        $height = $size[1];

                        $query = "INSERT INTO imagen (nombre_imagen, ruta, ancho, alto, descripcion, ingrediente_id_ingred, receta_id_receta, restaurante_id_rest)
                         VALUES (:nombreImagen, :ruta, :ancho, :alto, :descripcion, :idIngrediente, :idReceta, :idRestaurante)";
                        
                        $st = $db->prepare($query);

                        $st->bindParam(':nombreImagen', $nameImage);
                        $st->bindParam(':ruta', $ruteImage);
                        $st->bindParam(':ancho', $widthImage);
                        $st->bindParam(':alto', $heightImage);
                        $st->bindParam(':descripcion', $description);
                        $st->bindParam(':idIngrediente', $idIngredient);
                        $st->bindParam(':idReceta', $idRecipe);
                        $st->bindParam(':idRestaurante', $idRestaurant);

                        $nameImage = $image["name"];
                        $ruteImage = "src='" . $rute . "'";
                        $widthImage = $width;
                        $heightImage = $height;
                        $description = null;
                        $idIngredient = null;
                        $idRecipe = $recipeId;
                        $idRestaurant = null;

                        $st->execute();
                    }
                }

                $db->commit();

                echo "<p>Tu receta se ha añadido con éxito</p>";

            }catch(Exception $e){
                echo "<h2>Error</h2>";
                echo "<p>Error al intentar subir la receta: " . $e->getMessage() . "</p>";
                $db->rollback();
                die();
            }
        }
    }

  } catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>Error al intentar conectarse a la base de datos: " . $e->getMessage() . "</p>";
    die();
  }
  
echo "</div>";
include_once("pie.php"); ?>

<?php

function validation($recipe, $db){
    
    $validator = [
        'required' => [
            'callback' => function ($item , $message) {
                if(is_array($item)){
                    return count($item) > 0;
                }else{
                    return !empty($item);
                }
            }
        ],
        'alpha' => [
            'callback' => function ($item, $message) {
                    return preg_match('/^[A-Za-záéíóúñ\s]*/', $item); 
            }
        ],
        'alphanum' => [
            'callback' => function ($item, $message) {
                return preg_match('/^[0-9A-Za-záéíóúñ\s]*/', $item); 
            }
        ],
        'num' => [
            'callback' => function ($item, $message) {
                    return preg_match('/^[0-9]*/', $item); 
            }
        ],
        'numbers' => [
            'callback' => function ($item, $message) {
                    $ok = true;
                    foreach($item as $key => $value){
                        if(!preg_match('/^[0-9]*/', $value)){
                            $ok = false;
                            break;
                        }
                    }
                    return $ok;
            }
        ],
        'email' => [
            'callback' => function ($item, $message) { 
                if(empty($item)){
                    return true;
                }else{
                    return filter_var($item, FILTER_VALIDATE_EMAIL);
                }  
            }
        ]
    ];

    $assignments = [
        'titulo'                => ['required' => "El nombre de la receta no puede estar vacío", 'alphanum' => "El nombre de la receta debe estar compuesto de números y letras"],
        'multiple'              => ['numbers' => "El valor del tipo de receta debe ser numérico"],
        'provincia'             => ['required' => "La provincia no puede estar vacío", 'alpha' => "La provincia debe estar compuesta de números y letras"],
        'dificultad'            => ['required' => "La dificultad no puede estar vacía", 'alpha' => "La dificultad sólo puede contener letras"],
        'comensales'            => ['required' => "Los comensales de la receta no puede estar vacío", 'num' => "Los comensales sólo pueden contener números"],
        'duracion'              => ['required' => "La duración de la receta no puede estar vacía", 'num' => "La duración debe ser un número"],
        'preparacion'           => ['required' => "La preparación de la receta no puede estar vacía", 'alphanum' => "La preparación de la receta sólo puede contener letras y números"],
        'correo'                => ['email' => "El correo debe ser un email válido"],
        'observaciones'         => ['alpha' => "Las observaciones sólo puede contener letras y números"]
    ];

    try{
            
        $query = "SELECT id_ingred FROM ingrediente";
    
        $st = $db->prepare($query);
    
        $st->execute();
    
        $ingredients = $st->fetchAll(PDO::FETCH_ASSOC);
    
        $query = "SELECT id_tipo_receta FROM tipo_receta";
    
        $st = $db->prepare($query);
    
        $st->execute();
    
        $recipeTypes = $st->fetchAll(PDO::FETCH_ASSOC);
    
        $query = "SELECT * FROM provincia";
    
        $st = $db->prepare($query);
    
        $st->execute();
    
        $provinces = $st->fetchAll(PDO::FETCH_ASSOC);
    
    } catch (PDOException $e) {
        echo "<h2>Error</h2>";
        echo '¡Error!: ',$e->getMessage(),'<br />';
        die();
    } catch (Throwable $e) {
        echo "<h2>Error</h2>";
        echo '¡Error!: ',$e->getMessage(),'<br />';
        die();
    }
    
    $idIngredients = [];
    
    foreach($ingredients as $ingredient){
        array_push($idIngredients, $ingredient["id_ingred"]);
    }
    
    $idRecypeType = [];
    
    foreach($recipeTypes as $recipeType){
        array_push($idRecypeType, $recipeType["id_tipo_receta"]);
    }
    
    
    $idProvinces = [];
    
    foreach($provinces as $province){
        array_push($idProvinces, $province["id_provincia"]);
    }

    $ingredientsSelected = [];

    for($i=1; $i <= intval($recipe["contador"]); $i++){
        if(! empty($recipe["cant_" . $i])){
            $array = [
                'cant_' . $i => ["required" => "La cantidad de los ingredientes no puede estar vacía", "num" => "La cantidad de los ingredientes deben ser un número"],
                'medi_' . $i => ["alpha" => "El tipo de medida de los ingredientes sólo puede contener letras"],
                'prod_' . $i => ["required" => "Debes seleccionar un ingrediente", "num" => "El valor del ingrediente elegido debe ser un número"]
            ];
        }

        array_push($ingredientsSelected, $recipe['prod_'. $i]);

        $assignments = array_merge($assignments, $array);
    }

    $optionalIngredientsSelected = [];

    for($i=1; $i <= intval($recipe["contador2"]); $i++){
        if(! empty($recipe["cant2_" . $i])){
            $array = [
                'cant2_' . $i => ["required" => "La cantidad de los ingredientes no puede estar vacía", "num" => "La cantidad de los ingredientes deben ser un número"],
                'medi2_' . $i => ["alpha" => "El tipo de medida de los ingredientes sólo puede contener letras"],
                'prod2_' . $i => ["required" => "Debes seleccionar un ingrediente", "num" => "El valor del ingrediente elegido debe ser un número"]
            ];

            array_push($optionalIngredientsSelected, $recipe['prod2_'. $i]);

            $assignments = array_merge($assignments, $array);
            }
        }

    $error_message = "";
    foreach ($recipe as $field => $item) {
        if(in_array($field, array_keys($assignments))){
            foreach ($assignments[$field] as $key => $option) {
                if (! $validator[$key]['callback']($item, $option)) {
                    $message = $option;
                    $error_message = $error_message.  "<p>" . $message . "</p>";
                }
            }
        }
    }

    $ok = true;
    foreach($ingredientsSelected as $item){
        if(!in_array($item, $idIngredients)){
            $ok = false;
            break;
        }
    }

    if(!$ok){
        $error_message = $error_message . "<p>Los ingredientes principales seleccionados no son correctos</p>";
    }

            
    $ok = true;
    foreach($optionalIngredientsSelected as $item){
        if(!in_array($item, $idIngredients)){
            $ok = false;
            break;
        }
    }

    if(!$ok){
        $error_message = $error_message . "<p>Los ingredientes opcionales seleccionados no son correctos</p>";
    }

    $ok = true;
    foreach($recipe["multiple"] as $recipeType){
        if(!in_array($recipeType, $idRecypeType)){
            $ok = false;
            break;
        }
    }

    if(!$ok){
        $error_message = $error_message . "<p>Los tipos de recetas seleccionados no son correctos</p>";
    }

    if(!in_array($recipe["provincia"], $idProvinces)){
        $error_message = $error_message . "<p>La provincia seleccionada no es correcta</p>";
    }

    if($error_message != ""){
        echo "<h2>Error</h2>";
        echo $error_message;
        return false;
    }else{
        return true;
    }
}

?>


