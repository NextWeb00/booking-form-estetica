<?php
$prenotazioniFile = "prenotazioni.json";
$prenotazioni = [];

if (file_exists($prenotazioniFile)) {
    $prenotazioni = json_decode(file_get_contents($prenotazioniFile), true);
}

// Raggruppa prenotazioni per nome, telefono, trattamento, estetista e data
$gruppate = [];

foreach ($prenotazioni as $p) {
    $key = $p['nome'] . '|' . $p['telefono'] . '|' . $p['trattamento'] . '|' . $p['estetista'] . '|' . $p['data'];
    if (!isset($gruppate[$key])) {
        $gruppate[$key] = $p;
        $gruppate[$key]['orari'] = [];
    }
    $gruppate[$key]['orari'][] = $p['ora'];
}

// Ordina gli orari per ogni prenotazione
foreach ($gruppate as &$prenotazione) {
    sort($prenotazione['orari']);
}
unset($prenotazione);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Tabella Prenotazioni</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #333;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Tabella Prenotazioni</h1>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefono</th>
                <th>Trattamento</th>
                <th>Estetista</th>
                <th>Data</th>
                <th>Orari</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gruppate as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= htmlspecialchars($p['telefono']) ?></td>
                <td><?= htmlspecialchars($p['trattamento']) ?></td>
                <td><?= htmlspecialchars($p['estetista']) ?></td>
                <td><?= htmlspecialchars($p['data']) ?></td>
                <td><?= htmlspecialchars(implode(', ', $p['orari'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
