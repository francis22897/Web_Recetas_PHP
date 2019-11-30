<?php

include_once("cabecera.php");
include_once("menu.php");
include_once("col_izq.php");

echo "<div id='estiloglosario'>";

try{
    $db = new PDO('mysql:host=localhost;dbname=bd_recetas', 'root', 'root');
    
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM glosario";

    $st = $db->prepare($query);

    $st->execute();

    $glosary = $st->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($glosary) > 0){
        foreach($glosary as $expression): ?>

        <div>
            <p><b><?php echo $expression["expresion"]; ?></b></p>
            <p><?php echo $expression["definicion"]; ?></p>
        </div>

        <?php endforeach;
    }

} catch (PDOException $e) {
    echo '¡Error!: ',$e->getMessage(),'<br />';
    die();
} catch (Throwable $e) {
    echo '¡Error!: ',$e->getMessage(),'<br />';
    die();
}


include_once("pie.php");

?>