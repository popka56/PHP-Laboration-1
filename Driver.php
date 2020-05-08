<?php
class Driver {
  //Properties
  public $path = array();
  public $alwaysTryFirst;

  private $error = false;
  private $errorMsg = "";

  //Methods

  //Körs automatiskt när man skapar en ny Driver
  function __construct(string $alwaysTryFirst, int $numberOfRoadParts)
  {
    if($alwaysTryFirst == "forward" || $alwaysTryFirst == "left" || $alwaysTryFirst == "right"){
      //Vi sparar denna så vi kan använda i metoderna senare
      $this->alwaysTryFirst = $alwaysTryFirst;
      $this->path = array_pad($this->path, $numberOfRoadParts, $alwaysTryFirst);
    }
    else if($alwaysTryFirst == ""){
      $this->error = true;
      $this->errorMsg = "⚠️Error! You must enter a valid string value.\n";
    }
    else{
      //Skriv error om man försöker skriva ut datan om det inte är giltigt
      $this->error = true;
      $this->errorMsg = "⚠️Error! The value '" . $alwaysTryFirst . "' is not a valid string. It must be a string value of 'forward', 'left' or 'right'.\n";
    }
  }

  //Hämta data
  function get_alwaysTryFirst() {
    if($this->error){
      return $this->errorMsg;
    }
    else{
      return $this->alwaysTryFirst;
  }

  }
  function get_driverRoadMap() {
    if($this->error){
      return $this->errorMsg;
    }
    else{
      return $this->path;
    }
  }

  function get_error() {
    if($this->error){
      return true;
    }
    else{
      return false;
    }
  }

  function get_errorMsg() {
    return $this->errorMsg;
  }

  //Sätt data
  function set_alwaysTryFirst(string $alwaysTryFirst){
    if($alwaysTryFirst == "forward" || $alwaysTryFirst == "left" || $alwaysTryFirst == "right"){
      $this->error = false;
      $this->alwaysTryFirst = $alwaysTryFirst;
    }
    else{
      $this->error = true;
      $this->errorMsg = "⚠️Error! The value '" . $alwaysTryFirst . "' is not a valid string. It must be a string value of 'forward', 'left' or 'right'.\n";
    }
  }

  //Körs automatiskt i slutet
  function __destruct()
  {
      
  }

}
?>