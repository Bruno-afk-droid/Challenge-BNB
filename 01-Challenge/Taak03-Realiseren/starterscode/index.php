<?php
// Je hebt een database nodig om dit bestand te gebruiken....
include 'database.php';
if (!isset($db_conn)) { //deze if-statement checked of er een database-object aanwezig is. Kun je laten staan.
    return;
}

$database_gegevens = null;
$poolIsChecked = false;
$bathIsChecked = false;
$bbqIsChecked = false;
$wifiIsChecked = false;
$fireplaceIsChecked = false; 


$sql = "SELECT * FROM `homes` "; //Selecteer alle huisjes uit de database

if (isset($_GET['filter_submit'])) {
    $i=0;
    $sql .= "WHERE ";
    if (isset($_GET['ligbad'])) { // Als ligbad is geselecteerd filter dan de zoekresultaten
        $bathIsChecked = true;
        if($i>0) $sql .= "AND ";
        $sql .= "bath_present>0 "; // query die zoekt of er een BAD aanwezig is.
        $i++;
    }

    if (isset($_GET['zwembad'])) {
        $poolIsChecked = true;

        if($i>0) $sql .= "AND ";
        $sql .= "pool_present>0 "; // query die zoekt of er een ZWEMBAD aanwezig is.
        $i++;
    }

    if (isset($_GET['bbq'])) {
        $bbqIsChecked = true;

        if($i>0) $sql .= "AND ";
        $sql .= "bbq_present>0 "; // query die zoekt of er een ZWEMBAD aanwezig is.
        $i++;
    }

    if (isset($_GET['wifi'])) {
        $wifiIsChecked = true;

        if($i>0) $sql .= "AND ";
        $sql .= "wifi_present>0 "; // query die zoekt of er een ZWEMBAD aanwezig is.
        $i++;
    }

    if (isset($_GET['fireplace'])) {
        $fireplaceIsChecked = true;

        if($i>0) $sql .= "AND ";
        $sql .= "fireplace_present>0 "; // query die zoekt of er een ZWEMBAD aanwezig is.
        $i++;
    }

    if($i==0) $sql.="*";

}


if (is_object($db_conn->query($sql))) { //deze if-statement controleert of een sql-query correct geschreven is en dus data ophaalt uit de DB
    $database_gegevens = $db_conn->query($sql)->fetchAll(PDO::FETCH_ASSOC); //deze code laten staan
}

function getDatabase_gegevens($sql){
    $DB = new PDO('mysql:host=localhost;dbname=cottagerentals', 'root', '');
    if (is_object($DB->query($sql))) { //deze if-statement controleert of een sql-query correct geschreven is en dus data ophaalt uit de DB
        return $DB->query($sql)->fetchAll(PDO::FETCH_ASSOC); //deze code laten staan
    }
    return null;
}
if(isset($_GET["gekozen_huis"])){
    $gekozen_huis = $_GET["gekozen_huis"];
}

if(isset($_GET["aantal_personen"])){
    $personen = $_GET["aantal_personen"];
}
if(isset($_GET["aantal_dagen"])){
    $dagen = $_GET["aantal_dagen"];
}
if(isset($_GET["beddengoed"])){
    $beddengoed = $_GET["beddengoed"];
}
$sql2 = "SELECT * FROM `homes` WHERE `name`= 'IJmuiden Cottage' ";
$D = getDatabase_gegevens($sql2);
if(isset($personen)&&isset($dagen))
foreach ($D as $H) :
$prijs=($H['price_p_p_p_n']*(int)$personen)*(int)$dagen;
endforeach;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <form name="Reserveer" action="">
    <header>
        <h1>Quattro Cottage Rental</h1>
    </header>
    <main>
        <div class="left">
            <div id="mapid"></div>
            <div class="book">
                <h3>Reservering maken</h3>
                <div class="form-control">
                    <label for="aantal_personen">Vakantiehuis</label>
                    <select name="gekozen_huis" id="gekozen_huis">
                        <option value="1">IJmuiden Cottage</option>
                        <option value="2">Assen Bungalow</option>
                        <option value="3">Espelo Entree</option>
                        <option value="4">Weustenrade Woning</option>
                    </select>
                </div>
                <div class="form-control">
                    <label for="aantal_personen">Aantal personen</label>
                    <input type="number" name="aantal_personen" id="aantal_personen">
                </div>
                <div class="form-control">
                    <label for="aantal_dagen">Aantal dagen</label>
                    <input type="number" name="aantal_dagen" id="aantal_dagen">
                </div>
                <div class="form-control">
                    <h5>Beddengoed</h5>
                    <label for="beddengoed_ja">Ja</label>
                    <input type="radio" id="beddengoed_ja" name="beddengoed" value="ja">
                    <label for="beddengoed_nee">Nee</label>
                    <input type="radio" id="beddengoed_nee" name="beddengoed" value="nee">
                </div>

                <button type="submit" name="Reserveer_submit">Reserveer huis</button>
            </div>
            <div class="currentBooking">
                <div class="bookedHome"></div>
                <div class="totalPriceBlock">Totale prijs &euro;<span class="totalPrice"><?php if(isset($prijs)) echo $prijs; else echo 0;?></span></div>
            </div>
        </div>
        </form>
        <div class="right">
            <div class="filter-box">
                <form class="filter-form" method= "get">
                    <div class="form-control">
                        <a href="index.php">Reset Filters</a>
                        <h1><?php echo $sql?></h1>
                    </div>
                    <div class="form-control">
                        <label for="ligbad">Ligbad</label>
                        <input type="checkbox" id="ligbad" name="ligbad" value="ligbad" <?php if ($bathIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="zwembad">Zwembad</label>
                        <input type="checkbox" id="zwembad" name="zwembad" value="zwembad" <?php if ($poolIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="bbq">bbq</label>
                        <input type="checkbox" id="bbq" name="bbq" value="bbq" <?php if ($bbqIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="wifi">wifi</label>
                        <input type="checkbox" id="wifi" name="wifi" value="wifi" <?php if ($wifiIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="fireplace">fireplace</label>
                        <input type="checkbox" id="fireplace" name="fireplace" value="fireplace" <?php if ($fireplaceIsChecked) echo 'checked' ?>>
                    </div>
    
                    <button type="submit" name="filter_submit">Filter</button>
                </form>
                <div class="homes-box">
                    <?php if (isset($database_gegevens) && $database_gegevens != null) : ?>
                        <?php foreach ($database_gegevens as $huisje) : ?>
                            <h4>
                                <?php echo $huisje['name']; ?>
                            </h4>
                                <?php
                                      
                                      
                                      
                                      
                                      if($huisje['name']=="IJmuiden Cottage") $p = "IJmuiden_Cottage.jpg";
                                      if($huisje['name']=="Weustenrade Woning") $p = "Weustenrade_Woning.jpg";
                                      if($huisje['name']=="Assen Bungalow") $p = "Assen_Bungalow.jpg";
                                      if($huisje['name']=="Espelo Entree") $p = "Espelo.jpg";
                                ?>
                                <img src= <?php echo $p; ?>
                            <p>
                                <?php echo $huisje['description']; ?>
                            </p>
                            <div class="kenmerken">
                                <h6>Kenmerken</h6>
                                <ul>

                                    <?php
                                    if ($huisje['bath_present'] ==  1) {
                                        echo "<li>Er is ligbad!</li>";
                                    }
                                    ?>


                                    <?php
                                    if ($huisje['pool_present'] ==  1) {
                                        echo "<li>Er is zwembad!</li>";
                                    }
                                    ?>

                                    <?php
                                    if ($huisje['bbq_present'] ==  1) {
                                        echo "<li>Er is een bbq!</li>";
                                    }
                                    ?>


                                    <?php
                                    if ($huisje['wifi_present'] ==  1) {
                                        echo "<li>Er is wifi!</li>";
                                    }
                                    ?>

                                    <?php
                                    if ($huisje['fireplace_present'] ==  1) {
                                        echo "<li>Er is een openhaard!</li>";
                                    }
                                    ?>

                                </ul>

                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>
    <footer>
        <div></div>
        <div>copyright Quattro Rentals BV.</div>
        <div></div>

    </footer>
    <script src="js/map_init.js"></script>
 
    <script>
        // De verschillende markers moeten geplaatst worden. Vul de longitudes en latitudes uit de database hierin
        var coordinates = [];
        var bubbleTexts = [];
        var long,lat,text;


         //bubbleTexts.push("test locatie");
      //  coordinates.push([52.28785, 4.83866]);  


        //if(isset($database_gegevens) && $database_gegevens != null) {
            <?php foreach($database_gegevens as $cor) : ?>   
                <?php 
                     
                    $decLong =  (float)$cor['longitude'];
                      $decLat = (float)$cor['latitude'];
                      $text =   (string)$cor['description'];
                      $arrCor = array($decLong,$decLat);
                      $strCor= '['.$decLong.','.$decLat.']';
                    ?> 
                lat = <?php echo (float)$decLat; ?>;
                long = <?php echo (float)$decLong; ?>;
                text = <?php echo "'{$text}'"; ?>;
  
                coordinates.push([lat, long]);
                bubbleTexts.push(text);
               //coordinates.push([<?php   //(float)$decLong; ?>, <?php   //(float)$decLat; ?>]);  
              // coordinates.push([52.28785, 4.83866]); 
                //bubbleTexts.push($cor['description']);
            <?php endforeach; ?>
            // coordinates.push([$cor['longitude'],$cor['latitude']]);  
                
        
        

    </script>
    <script src="js/place_markers.js"></script>
</body>

</html>