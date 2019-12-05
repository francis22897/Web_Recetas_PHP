<?php

include_once("cabecera.php");
include_once("menu.php");
include_once("col_izq.php");

echo "<div id='estiloRecetas'>";

?>

<!-- Creo dos formularios, uno para buscar recetas por nombre y otro para buscar por ingrediente -->

<form action="mostrarBDrecetas.php" method="POST">

    <div class="fields">
        <label for="recipeSearch">Buscar por receta:</label>
        <input type="text" name="recipeSearch" value="<?php if(isset($_POST["recipeSearch"])){ echo $_POST["recipeSearch"]; } ?>">
    </div>
    <input type="submit" value="Buscar">
</form>

<form action="mostrarBDrecetas.php" method="POST">

    <div class="fields">
        <label for="ingredientSearch">Buscar por ingrediente:</label>
        <input type="text" name="ingredientSearch" value="<?php if(isset($_POST["ingredientSearch"])){ echo $_POST["ingredientSearch"]; } ?>">
    </div>
    <input type="submit" value="Buscar">
</form>

<?php

//En función del parámetro de la consulta GET, llamo a las funciones para obtener las recetas
if(count($_GET) != 0){
    
    if(isset($_GET["tipo"])){
        $type = $_GET["tipo"];

        switch($type) {
            case "provincia":
                getByProvince();
                break;
            case "especial":
                getBySpecial();
                break;
            case "vegetariana":
            case "vegana":
            case "light":
                getByStyle($type);
                break;
            case "celiaco":
                $type = "celiacos";
                getByStyle($type);
                break;
            case "receta":
                getByRecipe();
                break;
        }
    }

    //En caso de no haber consulta GET, se comprueba si hay consulta POST
    //En caso afirmativo, quiere decir que se ha hecho una búsqueda por el formulario

}else{
    //Si el POST proviene del formulario de recetas
    if(isset($_POST["recipeSearch"])){
        $searchText = $_POST["recipeSearch"];

        //Consulta para obtener las recetas cuyo titulo coincida con el texto buscado
        if(!empty($searchText)){
            try{
                $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
                
                $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
                $query = "SELECT * FROM receta WHERE LOWER(titulo) LIKE :text";
        
                $st = $db->prepare($query);
                $st->bindParam(':text', $text);
                $text = '%' . $searchText . '%';
        
                $st->execute();
    
                $recipes = $st->fetchAll(PDO::FETCH_ASSOC);
                
                showRecipes($recipes);
        
            } catch (PDOException $e) {
                echo '¡Error!: ',$e->getMessage(),'<br />';
                die();
            } catch (Throwable $e) {
                echo '¡Error!: ',$e->getMessage(),'<br />';
                die();
            }
        }
    }
    //Si el POST proviene del formulario de ingredientes
    if(isset($_POST["ingredientSearch"])){
        $searchText = $_POST["ingredientSearch"];
    //Consulta para obtener las recetas con algun ingrediente que coincida con el texto buscado
        if(!empty($searchText)){
            try{
                $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
                
                $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
                $query = "SELECT * FROM receta JOIN ingredientes_principales ON receta.id_receta = ingredientes_principales.id_receta 
                JOIN ingrediente ON ingredientes_principales.id_ingred = ingrediente.id_ingred WHERE ingrediente.nombre LIKE :text";
        
                $st = $db->prepare($query);
                $st->bindParam(':text', $text);
                $text = '%' . $searchText . '%';
        
                $st->execute();
    
                $recipes = $st->fetchAll(PDO::FETCH_ASSOC);
                
                showRecipes($recipes);
        
            } catch (PDOException $e) {
                echo '¡Error!: ',$e->getMessage(),'<br />';
                die();
            } catch (Throwable $e) {
                echo '¡Error!: ',$e->getMessage(),'<br />';
                die();
            }
        }
    }
}

//Función para obtener las recetas por provincia
function getByProvince() {

    //Si ha recibido el POST 
    if(isset($_POST["selectprov"])){
        $province = $_POST["selectprov"];

        //Consulta para obtener las recetas en función de la provincia recibida
        try{
            $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
            
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
            $query = "SELECT * FROM receta JOIN receta_provincia ON id_receta = receta_id_receta JOIN provincia ON id_provincia = provincia_id_provincia WHERE nombre=:nombre";
    
            $st = $db->prepare($query);
            $st->bindParam(':nombre', $name);
            $name = $province;
    
            $st->execute();

            $recipes = $st->fetchAll(PDO::FETCH_ASSOC);
            
            //Muestro las recetas
            showRecipes($recipes);
    
        } catch (PDOException $e) {
            echo '¡Error!: ',$e->getMessage(),'<br />';
            die();
        } catch (Throwable $e) {
            echo '¡Error!: ',$e->getMessage(),'<br />';
            die();
        }
    }

}

//Función para buscar recetas por provincia y su especialidad
function getBySpecial() {

    //Si ha recibido el POST
    if(isset($_POST["selectprov2"]) && isset($_POST["selectesp"])){
        $province = $_POST["selectprov2"];
        $speciality = $_POST["selectesp"];

        //Consulta para obtener recetas en función de la provincia y su especialidad
        try{
            $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
            
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
            $query = "SELECT * FROM receta JOIN recetas_de_restaurantes ON id_receta = receta_id_receta JOIN restaurante ON restaurante.id_rest = recetas_de_restaurantes.id_rest WHERE provincia=:provincia AND especialidad=:especialidad";
    
            $st = $db->prepare($query);
            $st->bindParam(':provincia', $provinceName);
            $st->bindParam(':especialidad', $specialityName);
            $provinceName = $province;
            $specialityName = $speciality;
    
            $st->execute();

            $recipes = $st->fetchAll(PDO::FETCH_ASSOC);
            
            //Muestro las recetas
            showRecipes($recipes);
    
        } catch (PDOException $e) {
            echo '¡Error!: ',$e->getMessage(),'<br />';
            die();
        } catch (Throwable $e) {
            echo '¡Error!: ',$e->getMessage(),'<br />';
            die();
        }

    }
}

//Función que busca recetas en función de si es vegetariana, vegana, light, o celiaca
//Entrada: el tipo de receta por la que debe buscar
function getByStyle($style) {

    //Consulta para buscar recetas en función si el tipo de receta recibido está activo
    try{
        $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
        
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM receta WHERE " . $style . "=1";

        $st = $db->prepare($query);

        $st->execute();

        $recipes = $st->fetchAll(PDO::FETCH_ASSOC);
        
        //Mostrar las recetas
        showRecipes($recipes);

    } catch (PDOException $e) {
        echo '¡Error!: ',$e->getMessage(),'<br />';
        die();
    } catch (Throwable $e) {
        echo '¡Error!: ',$e->getMessage(),'<br />';
        die();
    }
}

//Función que busca recetas por su identificador
function getByRecipe(){
    //Comprueba que haya recibido el POST
    if(isset($_POST["recipe"])){
        $idRecipe = $_POST["recipe"];
        //Consulta para obtener recetas por su id
        try{
            $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
            
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
            $query = "SELECT * FROM receta WHERE id_receta=:id";
    
            $st = $db->prepare($query);
            $st->bindParam(':id', $id);
            $id = $idRecipe;
    
            $st->execute();

            $recipes = $st->fetchAll(PDO::FETCH_ASSOC);
            
            //Muestro la receta
            showRecipes($recipes);
    
        } catch (PDOException $e) {
            echo '¡Error!: ',$e->getMessage(),'<br />';
            die();
        } catch (Throwable $e) {
            echo '¡Error!: ',$e->getMessage(),'<br />';
            die();
        }
    }
}

//Función para mostrar las recetas
//Entrada: las recetas que debe mostrar
function showRecipes($recipes){
    //Si al array está vacío, no se han encontrado recetas
    if(count($recipes) == 0){
        echo "<h2>No se han encontrado recetas</h2>";
    }else{
        //En caso de haber alguna receta, las recorro con un bucle
        foreach($recipes as $recipe){
            try{
                $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
                
                $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
                //Consulta para obtener la suma de las propiedades de todos los ingredientes de la receta
                $query = "SELECT sum(calorias_100g) as calorias, sum(proteinas_100g) as proteinas, sum(hidratos_100g) as hidratos, 
                sum(grasas_saturadas_100g) as grasas_saturadas, sum(grasas_totales_100g) as grasas_totales, 
                sum(grasas_monoinsaturadas_100g) as grasas_monoinsaturadas, sum(grasas_poliinsaturadas_100g) as grasas_poliinsaturadas, 
                sum(sodio_100g) as sodio, sum(fibra_100g) as fibra FROM ingrediente JOIN ingredientes_principales ON 
                ingrediente.id_ingred = ingredientes_principales.id_ingred JOIN receta ON 
                receta.id_receta = ingredientes_principales.id_receta WHERE receta.id_receta = :id";
        
                $st = $db->prepare($query);
                $st->bindParam(':id', $id);
                $id = $recipe["id_receta"];
        
                $st->execute();
    
                $properties = $st->fetchAll(PDO::FETCH_ASSOC);
                
                //Consulta para obtener el nombre de los ingredientes principales, la cantidad necesaria y la medida (gramos o unidades)
                $query = "SELECT ingrediente.nombre, ingredientes_principales.cantidad, ingredientes_principales.medida FROM ingrediente JOIN ingredientes_principales on ingredientes_principales.id_ingred = ingrediente.id_ingred 
                WHERE ingredientes_principales.id_receta = :id";

                $st = $db->prepare($query);
                $st->bindParam(':id', $id);
                $id = $recipe["id_receta"];

                $st->execute();

                $ingredients = $st->fetchAll(PDO::FETCH_ASSOC);

                //Consulta para obtener el nombre de los ingredientes opcionales, la cantidad necesaria y la medida (gramos o unidades)
                $query = "SELECT ingrediente.nombre, ingredientes_opcionales.cantidad, ingredientes_opcionales.medida FROM ingrediente JOIN ingredientes_opcionales on ingredientes_opcionales.id_ingred = ingrediente.id_ingred 
                WHERE ingredientes_opcionales.id_receta = :id;";

                $st = $db->prepare($query);
                $st->bindParam(':id', $id);
                $id = $recipe["id_receta"];

                $st->execute();

                $optionalIngredients = $st->fetchAll();

                //Consulta para obtener las imágenes de la receta
                $query = "SELECT * FROM imagen WHERE imagen.receta_id_receta = :id";

                $st = $db->prepare($query);
                $st->bindParam(':id', $id);
                $id = $recipe["id_receta"];

                $st->execute();

                $images = $st->fetchAll(PDO::FETCH_ASSOC);

               ?>
                
                <!-- Muestro la información de la receta -->
                <h2><?php echo $recipe['titulo'] . ' para ' . $recipe['comensales'] . ' personas'; ?></h2>
                <p>Duración <?php echo $recipe["duracion"] . ' minutos' ?></p>
                <div>
                <!-- Recorro las imágenes -->
                <?php foreach($images as $image): ?>                  
                    <img <?php echo $image["ruta"]; ?> alt="<?php echo $image["nombre_imagen"]; ?>" height="250" width="250">
                <?php endforeach; ?>
                </div>
                <div class="ingredients">
                    <div>
                        <p><b>Ingredientes</b></p>
                        <ul>
                            <!-- Recorro los ingredientes principales -->
                            <?php foreach($ingredients as $ingredient){
                                if($ingredient["medida"] == "gramos") : ?>
                                    <li><p><?php echo $ingredient["nombre"] . " " . $ingredient["cantidad"] . " gramos" ?></p></li>
                                <?php else: ?>
                                    <li><p><?php echo $ingredient["nombre"] . " " . $ingredient["cantidad"] . " unidad/es" ?></p></li>
                                <?php endif;                                
                            } 
                            ?>
                        </ul>

                        <?php if(count($optionalIngredients) > 0) : ?>

                            <p><b>Ingredientes opcionales</b></p>
                            <ul>
                                <!-- En caso de haber ingredientes opcionales, los recorro -->
                                <?php foreach($optionalIngredients as $ingredient){
                                    if($ingredient["medida"] == "gramos") : ?>
                                        <li><p><?php echo $ingredient["nombre"] . " " . $ingredient["cantidad"] . " gramos" ?></p></li>
                                    <?php else: ?>
                                        <li><p><?php echo $ingredient["nombre"] . " " . $ingredient["cantidad"] . " unidad/es" ?></p></li>
                                    <?php endif;                                
                                }
                                ?>
                            </ul>

                        <?php endif; ?>
                    </div>
                    <div>
                        <!-- Creo una tabla para mostrar las propiedades de los ingredientes principales -->
                        <table border="1">
                            <thead>
                                <th>Propiedades</th>
                                <th>Cantidad (100g)</th>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach($properties as $propertie){
                                        foreach($propertie as $key => $value): ?>
                                            <tr>
                                                <td><?php echo $key; ?></td>
                                                <td><?php echo number_format($value, 2) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <!-- Creo un array con los utensilios necesarios para la receta -->
                <?php 
                
                    $utensils = [];
                    
                    if($recipe["horno"] == 1)
                        array_push($utensils, "horno");
                    if($recipe["microondas"] == 1)
                        array_push($utensils, "microondas");
                    if($recipe["batidora"] == 1)
                        array_push($utensils, "batidora");
                    if($recipe["thermomix"] == 1)
                        array_push($utensils, "thermomix");

                ?>

                <!-- En caso de haber algún utensilio, los muestro con un implode -->
                <?php if(count($utensils) > 0) : ?>
                    <div>
                        <p><b>Necesitaremos</b></p>
                        <p><?php echo implode(", ",  $utensils); ?></p>
                    </div>
                <?php endif; ?>

                </div>
                <div>
                    <p><b>Preparación</b></p>
                    <p><?php echo $recipe["preparacion"]; ?></p>
                </div>

                <!-- Creo un array con el tipo de receta que es -->
                <?php 
                
                    $type = [];
                    
                    if($recipe["celiacos"] == 1)
                        array_push($type, "Sin gluten");
                    if($recipe["light"] == 1)
                        array_push($type, "Light");
                    if($recipe["vegetariana"] == 1)
                        array_push($type, "Vegetariana");
                    if($recipe["vegana"] == 1)
                        array_push($type, "Vegana");
                ?>

                <!-- En caso de ser de algún tipo, los muestro con un implode -->
                <?php if(count($type) > 0) : ?>
                    <div>
                        <p><b>Características</b></p>
                        <p><?php echo implode(", ",  $type); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($recipe["observaciones"])) : ?>
                    <div>
                        <p><b>Observaciones</b></p>
                        <p><?php echo $recipe["observaciones"] ?></p>
                    </div>
                <?php endif; ?>

               <?php
        
            } catch (PDOException $e) {
                echo '¡Error!: ',$e->getMessage(),'<br />';
                die();
            } catch (Throwable $e) {
                echo '¡Error!: ',$e->getMessage(),'<br />';
                die();
            }
        }
        
    }

}

echo "</div>";
include_once("pie.php");

?>