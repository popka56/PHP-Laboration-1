<?php
    //Hämta klasserna vi använder
    include "Driver.php";
    include "Road.php";
    //Behövs för att spara värden i '$_SESSION'
    session_start();

    //Töm alla session värden
    function clearSession(){
        if (isset($_POST['clearSession'])) {
            session_destroy();
            echo "💬 The session data has been cleared!";
            unset($_POST['clearSession']);
        }
    }
    //Ta bort road filen, så en ny kan skapas
    function createNewRoad(){
        if(isset($_POST['createNewRoad'])){
            if(file_exists("road.csv")){
                //Ta bort filen
                unlink("road.csv");
                //Töm även sessions så det inte blir några krockar
                session_destroy();
                echo "💬 The session data has been cleared and a new road has been generated!";
            }
            unset($_POST['createNewRoad']);
        }
    }

    //Om vi inte skapat en väg gör vi det nu
    if(!file_exists("road.csv")){
        //Skapa vår väg
        $road = new Road();
        //Öppna och skriv över vår fil
        $myfile = fopen("road.csv", "w") or die("Unable to create file!");
        //Skriv över filen med vår vägs array värden
        for($i = 0; $i < count($road->get_road()); $i++){
            //Skriv bara ut radbrytning på varje värde förutom det allra sista
            if($i < (count($road->get_road()) - 1)){
                fwrite($myfile, $road->get_road()[$i] . "\n");
            }
            else{
                fwrite($myfile, $road->get_road()[$i]);   
            }
        }
        //Stäng filen
        fclose($myfile);
    }

    //Om vi inte har läst av vår fil gör vi det här
    if(!isset($_SESSION['fileRoadMap'])){
        //Ska kolla hur många delar av väg vi måste ta oss igenom
        $fileRoadMap = array();
        //Öppna och skriv ut innehåll av en fil, rad för rad
        $myfile = fopen("road.csv", "r") or die("Unable to open file!");
        // Spara varje rad i filen som ett index i vår array
        while(!feof($myfile)) {
            array_push($fileRoadMap, fgets($myfile));
        }
        //Ta bort whitespace
        $fileRoadMap = array_map('trim', $fileRoadMap);

        //Spara värderna för framtiden
        $_SESSION['numberOfRoadParts'] = count($fileRoadMap);
        $_SESSION['fileRoadMap'] = $fileRoadMap;

        //Stäng filen
        fclose($myfile);
    }
    
    //Finns drivern inte så gör vi den icke valid, vi vill inte tillåta mer än
    //tre drivers eftersom man inte ha rmer än tre val att göra när dem skapas
    if(!isset($_SESSION['driver1'])){
        $_SESSION['driver1'] = "";
    }
    if(!isset($_SESSION['driver2'])){
        $_SESSION['driver2'] = "";
    }
    if(!isset($_SESSION['driver3'])){
        $_SESSION['driver3'] = "";
    }

    //För att skilja på våra drivers och andra session värden
    if(!isset($_SESSION['number'])){
        $_SESSION['number'] = 1;
    }

    //Skapar och visar resultatet av vår förare
    function createDriver(){
        if($_SESSION['driver' . $_SESSION['number']] != "valid" && ($_SESSION['number'] <= 3)){
            //Om vi inte redan har en valid driver så skapa den
            $driver = new Driver($_POST['tryFirst'], $_SESSION['numberOfRoadParts']);
            //Ger det inget error så spara värdet
            if(!$driver->get_error()){
                $_SESSION['alwaysTryFirst'. $_SESSION['number']] = $driver->get_alwaysTryFirst();
                $_SESSION['driverRoadMap'. $_SESSION['number']] = $driver->get_driverRoadMap();
                $_SESSION['driver' . $_SESSION['number']] = "valid";
                //Och skriv ut resultat till användaren
                echo "💬 Driver(" . $_SESSION['number'] . ") created! See details below:" . nl2br("\n");
                echo "🚘 Will always start going: " . $_SESSION['alwaysTryFirst'. $_SESSION['number']] . nl2br("\n");
                $_SESSION['number']++;
            }
            else{
                //Annars skriv ut ett error meddelande och sätt driver till icke valid
                echo $driver->get_errorMsg();
                $_SESSION['driver' . $_SESSION['number']] = "";
            }
        }
        else{
            //Har det skapats så många drivers man får visas dem bara istället
            echo "⚠️Error! You cannot create any more drivers. See details below:" . nl2br("\n");
            echo "🚘 Driver(1) Will always start with going: " . $_SESSION['alwaysTryFirst1']  . nl2br("\n");
            echo "🚘 Driver(2) Will always start with going: " . $_SESSION['alwaysTryFirst2']  . nl2br("\n");
            echo "🚘 Driver(3) Will always start with going: " . $_SESSION['alwaysTryFirst3']  . nl2br("\n");
        }
    }

    //Funktionen som sätter igång våra drivers
    function drive(){
        //Vi kollar bara om första drivern är valid, eftersom den måste 
        //vara det för att en driver2 eller driver3 ska existera ändå
        if($_SESSION['driver1'] == "valid"){
            //Har vi en valid driver så skriv ut filens väg först för jämförelse
            echo "🛣️ The correct path:" . nl2br("\n");
            for($i = 0; $i < $_SESSION['numberOfRoadParts']; $i++){
                echo   ($i + 1) . ": " . $_SESSION['fileRoadMap'][$i] . nl2br("\n");
            }
            
            //Loopar igenom så många gånger som det finns drivers
            for($driver = 1; $driver <  $_SESSION['number']; $driver++){
            
            //Använder loopens index vara namnet på våra drivers
            echo "🚘 Driver(" . $driver . "):" . nl2br("\n");
            //Loop för körningen
            for($i = 0; $i < $_SESSION['numberOfRoadParts']; $i++){
                //Skriv ut filens väg för jämförelse
                echo  ($i + 1) . ": " . $_SESSION['driverRoadMap'. $driver][$i];
                //Börja jämföra array värden
                if($_SESSION['driverRoadMap' . $driver][$i] ==  $_SESSION['fileRoadMap'][$i]){
                    //Stämmer det så fortsätt köra loopen
                    echo  "✔️" . nl2br("\n");
                    //Är det sista loopen så ge avslutande meddelande
                    if(($i + 1) == $_SESSION['numberOfRoadParts']){
                        echo "💬 The driver has finished the course! 🎉🎉🎉" . nl2br("\n");
                    }
                }
                else{
                    //Stämmer det inte så ge fel, förbered nästa försök och avsluta loopen
                    echo  "❌" . nl2br("\n");
                    echo "💬 The driver has crashed! ☠️ Try again!" . nl2br("\n");
                    //Testa alltid alla tre åksätten i samma ordning
                    if($_SESSION['driverRoadMap' . $driver][$i] == "forward"){
                        $_SESSION['driverRoadMap' . $driver][$i] = "left";
                    }
                    else if($_SESSION['driverRoadMap' . $driver][$i] == "left"){
                        $_SESSION['driverRoadMap' . $driver][$i] = "right";
                    }
                    else if($_SESSION['driverRoadMap' . $driver][$i] == "right"){
                        $_SESSION['driverRoadMap' . $driver][$i] = "forward";
                    }
                //Hoppa ur loopen så användaren får klicka igen efter varje krasch
                break;
                }
            }
        }
            }
        else{
            //Annars visa error meddelande
            echo "⚠️Error! No valid driver exists. Create one first.";
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboration 1</title>
</head>
<body>
    <div>

        <h2>Clear Session Data</h2>
        <form method="post">
            <input type="submit" value="Clear Session" name="clearSession">
        </form>

        <?php
        if (isset($_POST['clearSession'])) {
            clearSession();
        }
        ?>

        <form method="post">
            <input type="submit" value="Create New Road" name="createNewRoad">
        </form>

        <?php
        if (isset($_POST['createNewRoad'])) {
            createNewRoad();
        }
        ?>

        <h2>Create drivers</h2>
        <p>Create a new driver by entering how it should react to a new road part.</p>
        <form method="post">
            <label>First try going...</label><br>
            <input type="" placeholder="forward, left or right" name="tryFirst"><br><br>
            <input type="submit" value="Create Driver" name="createDriver">
        </form>

        <?php
        if (isset($_POST['createDriver'])) {
            createDriver();
        }
        ?>

        <h2>Time to drive</h2>
        <p>Begin driving on the road.</p>
        <form method="post">
            <input type="submit" value="Go" name="beginDriving">
        </form>
        
        <?php
        if (isset($_POST['beginDriving'])) {
            drive();
        }
        ?>

    </div>

</body>
</html>