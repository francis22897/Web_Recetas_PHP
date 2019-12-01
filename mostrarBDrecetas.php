<?php

include_once("cabecera.php");
include_once("menu.php");
include_once("col_izq.php");

echo "<div id='estiloRecetas'>";

?>

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
            case "celiaco":
            case "light":
                getByStyle($type);
                break;
            case "receta":
                getByRecipe();
                break;
        }
    }

}else{
    if(isset($_POST["recipeSearch"])){
        $searchText = $_POST["recipeSearch"];

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

    if(isset($_POST["ingredientSearch"])){
        $searchText = $_POST["ingredientSearch"];

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

function getByProvince() {

    if(isset($_POST["selectprov"])){
        $province = $_POST["selectprov"];

        try{
            $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
            
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
            $query = "SELECT * FROM receta JOIN receta_provincia ON id_receta = receta_id_receta JOIN provincia ON id_provincia = provincia_id_provincia WHERE nombre=:nombre";
    
            $st = $db->prepare($query);
            $st->bindParam(':nombre', $name);
            $name = $province;
    
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

function getBySpecial() {
    if(isset($_POST["selectprov2"]) && isset($_POST["selectesp"])){
        $province = $_POST["selectprov2"];
        $speciality = $_POST["selectesp"];

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

function getByStyle($style) {

    try{
        $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
        
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM receta WHERE " . $style . "=1";

        $st = $db->prepare($query);

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


function getByRecipe(){
    if(isset($_POST["recipe"])){
        $idRecipe = $_POST["recipe"];
        try{
            $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
            
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
            $query = "SELECT * FROM receta WHERE id_receta=:id";
    
            $st = $db->prepare($query);
            $st->bindParam(':id', $id);
            $id = $idRecipe;
    
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

function showRecipes($recipes){
    

    if(count($recipes) == 0){
        echo "<h2>No se han encontrado recetas</h2>";
    }else{

        foreach($recipes as $recipe){
            try{
                $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
                
                $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
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
                
                $query = "SELECT ingrediente.nombre, ingredientes_principales.cantidad, ingredientes_principales.medida FROM ingrediente JOIN ingredientes_principales on ingredientes_principales.id_ingred = ingrediente.id_ingred 
                WHERE ingredientes_principales.id_receta = :id";

                $st = $db->prepare($query);
                $st->bindParam(':id', $id);
                $id = $recipe["id_receta"];

                $st->execute();

                $ingredients = $st->fetchAll(PDO::FETCH_ASSOC);

                $query = "SELECT ingrediente.nombre, ingredientes_opcionales.cantidad, ingredientes_opcionales.medida FROM ingrediente JOIN ingredientes_opcionales on ingredientes_opcionales.id_ingred = ingrediente.id_ingred 
                WHERE ingredientes_opcionales.id_receta = :id;";

                $st = $db->prepare($query);
                $st->bindParam(':id', $id);
                $id = $recipe["id_receta"];

                $st->execute();

                $optionalIngredients = $st->fetchAll();

                $query = "SELECT * FROM imagen WHERE imagen.receta_id_receta = :id";

                $st = $db->prepare($query);
                $st->bindParam(':id', $id);
                $id = $recipe["id_receta"];

                $st->execute();

                $images = $st->fetchAll(PDO::FETCH_ASSOC);

               ?>

                <h2><?php echo $recipe['titulo'] . ' para ' . $recipe['comensales'] . ' personas'; ?></h2>
                <p>Duración <?php echo $recipe["duracion"] . ' minutos' ?></p>
                <div>
                <?php foreach($images as $image): ?>                  
                    <img <?php echo $image["ruta"]; ?> alt="<?php echo $image["nombre_imagen"]; ?>" height="250" width="250">
                <?php endforeach; ?>
                </div>
                <div class="ingredients">
                    <div>
                        <p><b>Ingredientes</b></p>
                        <ul>
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