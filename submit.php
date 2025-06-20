<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Metodo non consentito.";
    exit;
}

$nome = $_POST['nome'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$trattamento = $_POST['trattamento'] ?? '';
$estetista = $_POST['estetista'] ?? '';
$dataPrenotazione = $_POST['data'] ?? '';
$oraPrenotazione = $_POST['ora'] ?? '';

if (!$nome || !$telefono || !$trattamento || !$estetista || !$dataPrenotazione || !$oraPrenotazione) {
    http_response_code(400);
    echo "Dati mancanti.";
    exit;
}

// Controlla sabato (6) e domenica (7)
$giornoSettimana = date('N', strtotime($dataPrenotazione));
if ($giornoSettimana >= 6) {
    http_response_code(400);
    echo "Prenotazioni non disponibili il sabato e la domenica.";
    exit;
}

$orariDisponibili = [];
for ($h = 9; $h < 19; $h++) {
    $orariDisponibili[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ":00";
    $orariDisponibili[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ":30";
}

if (!in_array($oraPrenotazione, $orariDisponibili)) {
    http_response_code(400);
    echo "Orario non valido.";
    exit;
}

// Calcola quanti slot bloccare
$durata = ($trattamento === 'Ricostruzione') ? 7 : 1;
$indexInizio = array_search($oraPrenotazione, $orariDisponibili);
if ($indexInizio === false || ($indexInizio + $durata) > count($orariDisponibili)) {
    http_response_code(400);
    echo "Orario insufficiente per la durata del trattamento.";
    exit;
}

$orariDaBloccare = array_slice($orariDisponibili, $indexInizio, $durata);

$prenotazioniFile = 'prenotazioni.json';
$prenotazioni = file_exists($prenotazioniFile) ? json_decode(file_get_contents($prenotazioniFile), true) : [];

// Controlla se gli orari da bloccare sono già occupati per lo stesso estetista e data
foreach ($prenotazioni as $p) {
    if ($p['data'] === $dataPrenotazione && $p['estetista'] === $estetista && in_array($p['ora'], $orariDaBloccare)) {
        http_response_code(409);
        echo "Uno o più orari selezionati sono già prenotati.";
        exit;
    }
}

// Salva la prenotazione per ogni slot
foreach ($orariDaBloccare as $ora) {
    $prenotazioni[] = [
        'nome' => $nome,
        'telefono' => $telefono,
        'trattamento' => $trattamento,
        'estetista' => $estetista,
        'data' => $dataPrenotazione,
        'ora' => $ora
    ];
}

file_put_contents($prenotazioniFile, json_encode($prenotazioni, JSON_PRETTY_PRINT));

// Invio email (configura qui sotto)
$to = 'esempinextweb@gmail.com'; // cambia con la tua email
$subject = "Nuova prenotazione da $nome";
$message = "Dettagli prenotazione:\n\nNome: $nome\nTelefono: $telefono\nTrattamento: $trattamento\nEstetista: $estetista\nData: $dataPrenotazione\nOrari: " . implode(', ', $orariDaBloccare);
$headers = "From: no-reply@tuosito.com";

mail($to, $subject, $message, $headers);

echo "Prenotazione registrata con successo.";
?>
