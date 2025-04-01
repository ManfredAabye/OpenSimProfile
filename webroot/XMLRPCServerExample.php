<?php
$server = new XMLRPCServer();

// Beispiel-Methode für Classifieds-Update
$server->registerMethod("classified_update", function ($params) {
    return ["success" => true, "message" => "Classified wurde aktualisiert!", "data" => $params];
});

// Server starten (Anfragen empfangen)
$server->handleRequest();
?>
