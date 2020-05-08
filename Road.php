<?php
class Road {
  //Properties
  public $roadPath = array();

  //Methods

  //Körs automatiskt när man skapar en ny Road
  function __construct()
  {
    //Värden vi behöver för vår loop nedan
    $numberOfForwards = 0;
    $numberOfLefts = 0;
    $numberOfRights = 0;
    $allowedToTurn = "";
    $arrayValue = "";
    //Skapar vår slumpmässiga väg, 12 bitar, 4 av varje, kan inte bli left eller right utan att det blivit det motsatta först
    for($i = 0; $i < 12; $i++){
      $randomValue = rand(1, 3);
      switch($randomValue){
        case 1:
          if($numberOfForwards < 4){
            $numberOfForwards++;
            $arrayValue = "forward";
            array_push($this->roadPath, $arrayValue);
          }
          else{
            $i--;
          }
        break;
        case 2:
          if($numberOfLefts < 4 && ($allowedToTurn == "left" || $allowedToTurn == "")){
            $numberOfLefts++;
            $arrayValue = "left";
            $allowedToTurn = "right";
            array_push($this->roadPath, $arrayValue);
          }
          else{
            $i--;
          }
        break;
        case 3:
          if($numberOfRights < 4 && ($allowedToTurn == "right" || $allowedToTurn == "")){
            $numberOfRights++;
            $arrayValue = "right";
            $allowedToTurn = "left";
            array_push($this->roadPath, $arrayValue);
          }
          else{
            $i--;
          }
        break;
      }
    }
  }

  //Hämta data
  function get_road() {
      return $this->roadPath;
  }

  //Körs automatiskt i slutet
  function __destruct()
  {
      
  }

}
?>