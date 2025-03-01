<?php
include 'db.php';

$id = $_POST['id'];

$sql = "DELETE FROM viajes WHERE id='$id'";

if ($conn->query($sql)) {
    echo "Viaje eliminado con éxito";
} else {
    echo "Error: " . $conn->error;
}
?>
