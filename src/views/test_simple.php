<?php
// Test simple à placer à la racine
require 'utils/utils.php';
require 'utils/splAutoload.php';

echo "<h1>Test Simple d'Ajout de Participant</h1>";

// CHANGEZ CET ID PAR UN ID DE GROUPE EXISTANT
$testTricountId = 1;

try {
    $db = new PDO('mysql:host=mysql-con;dbname=database;charset=utf8', 'user', 'password');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Test 1 : Insertion minimale (sans user_id, sans email)</h2>";
    
    $testName = "Test Simple " . time();
    
    $sql = "INSERT INTO participants (tricount_id, alias_name, balance) VALUES (?, ?, 0)";
    $stmt = $db->prepare($sql);
    
    echo "<p>SQL : <code>$sql</code></p>";
    echo "<p>Valeurs : tricount_id=$testTricountId, alias_name=$testName</p>";
    
    if ($stmt->execute([$testTricountId, $testName])) {
        echo "✅ <strong>SUCCÈS !</strong> ID inséré : " . $db->lastInsertId() . "<br>";
        
        // Vérification
        $check = $db->prepare("SELECT * FROM participants WHERE id = ?");
        $check->execute([$db->lastInsertId()]);
        $participant = $check->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Données insérées :</h3>";
        echo "<pre>";
        print_r($participant);
        echo "</pre>";
        
    } else {
        echo "❌ <strong>ÉCHEC</strong><br>";
        print_r($stmt->errorInfo());
    }
    
    echo "<hr>";
    echo "<h2>Test 2 : Avec email</h2>";
    
    $testName2 = "Test Email " . time();
    $testEmail = "test@example.com";
    
    $sql2 = "INSERT INTO participants (tricount_id, alias_name, email, balance) VALUES (?, ?, ?, 0)";
    $stmt2 = $db->prepare($sql2);
    
    echo "<p>SQL : <code>$sql2</code></p>";
    echo "<p>Valeurs : tricount_id=$testTricountId, alias_name=$testName2, email=$testEmail</p>";
    
    if ($stmt2->execute([$testTricountId, $testName2, $testEmail])) {
        echo "✅ <strong>SUCCÈS !</strong> ID inséré : " . $db->lastInsertId() . "<br>";
    } else {
        echo "❌ <strong>ÉCHEC</strong><br>";
        print_r($stmt2->errorInfo());
    }
    
    echo "<hr>";
    echo "<h2>Test 3 : Vérifier si user_id peut être NULL</h2>";
    
    $testName3 = "Test UserID NULL " . time();
    
    $sql3 = "INSERT INTO participants (tricount_id, user_id, alias_name, balance) VALUES (?, NULL, ?, 0)";
    $stmt3 = $db->prepare($sql3);
    
    echo "<p>SQL : <code>$sql3</code></p>";
    
    if ($stmt3->execute([$testTricountId, $testName3])) {
        echo "✅ <strong>SUCCÈS !</strong> user_id NULL accepté<br>";
    } else {
        echo "❌ <strong>ÉCHEC</strong> user_id NULL refusé<br>";
        echo "<pre>";
        print_r($stmt3->errorInfo());
        echo "</pre>";
        
        echo "<p><strong>Solution :</strong> Il faut soit :</p>";
        echo "<ul>";
        echo "<li>Rendre user_id nullable : <code>ALTER TABLE participants MODIFY user_id INT NULL;</code></li>";
        echo "<li>Ou mettre une valeur par défaut : <code>ALTER TABLE participants MODIFY user_id INT DEFAULT NULL;</code></li>";
        echo "</ul>";
    }
    
    echo "<hr>";
    echo "<h2>Structure actuelle de la table participants :</h2>";
    $structure = $db->query("DESCRIBE participants")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    foreach ($structure as $col) {
        $highlight = ($col['Field'] === 'user_id' || $col['Field'] === 'email') ? ' style="background-color: yellow;"' : '';
        echo "<tr$highlight>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Points importants :</strong></p>";
    echo "<ul>";
    echo "<li>user_id doit avoir <code>Null = YES</code></li>";
    echo "<li>email doit avoir <code>Null = YES</code></li>";
    echo "</ul>";
    
} catch (\Exception $e) {
    echo "❌ <strong>ERREUR FATALE :</strong><br>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo '<p><a href="/groupe?id=' . $testTricountId . '">Retour au groupe</a></p>';
echo "<p><em>Supprimez ce fichier après les tests</em></p>";