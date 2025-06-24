<?php
// auteur: Ivanov
// functie: verwijder een vragen op basis van de id
include 'functions_vragen.php';
 
// Haal vragen uit de database
if(isset($_GET['id'])){
    DeleteVragen($_GET['id']);
 
    echo '<script>alert("Id: ' . $_GET['id'] . ' is verwijderd")</script>';
    echo "<script> location.replace('crud_vragen.php'); </script>";
}
?>