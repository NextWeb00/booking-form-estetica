<?php
$data = [
  "nome" => $_POST["nome"],
  "telefono" => $_POST["telefono"],
  "trattamento" => $_POST["trattamento"],
  "medico" => $_POST["medico"],
  "data" => $_POST["data"],
  "ora" => $_POST["ora"]
];

$file = 'prenotazioni.json';
$prenotazioni = [];

if (file_exists($file)) {
  $prenotazioni = json_decode(file_get_contents($file), true);
}

$prenotazioni[] = $data;
file_put_contents($file, json_encode($prenotazioni, JSON_PRETTY_PRINT));

// Redirect con conferma
echo "<script>alert('Prenotazione inviata con successo!');window.location.href='index.html';</script>";
?>
