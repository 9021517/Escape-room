<?php 
// auteur: Vitaliy Ivanov
// functie: algemene functies tbv hergebruik

function ConnectDb(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project4";
   
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    }
    catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

function CrudVragen($userRole) {
    // Haal alle vragen records uit de tabel
    $result = GetData("questions");

    // Print table
    PrintCrudVragen($result, $userRole);
}

// selecteer de data uit de opgeven table
function GetData($table) {
    // Connect database
    $conn = ConnectDb();

    // Select data uit de opgegeven table methode prepare
    $query = $conn->prepare("SELECT * FROM $table ORDER BY roomId, id");
    $query->execute();
    $result = $query->fetchAll();

    return $result;
}

// selecteer de rij van de opgeven id uit de table questions
function GetVragen($id){
    // Connect database
    $conn = ConnectDb();
 
    // Select data uit de opgegeven table methode prepare
    $query = $conn->prepare("SELECT * FROM questions WHERE id = :id");
    $query->execute([':id'=>$id]);
    $result = $query->fetch();
 
    return $result;
}

function PrintCrudVragen($result, $userRole) {
    if (empty($result)) {
        echo "<div class='no-data'>⚠️ Geen vragen gevonden in de database ⚠️</div>";
        return;
    }

    $table = "<table>";

    // Print header table
    $table .= "<tr>";
    $table .= "<th>🔢 ID</th>";
    $table .= "<th>❓ Vraag</th>";
    $table .= "<th>✅ Antwoord</th>";
    $table .= "<th>💡 Hint</th>";
    $table .= "<th>🏠 Kamer</th>";
    
    if ($userRole == 'admin') {
        $table .= "<th>⚙️ Acties</th>";
    }
    $table .= "</tr>";

    // Print elke rij
    foreach ($result as $row) {
        $table .= "<tr>";
        $table .= "<td><strong>" . htmlspecialchars($row['id']) . "</strong></td>";
        
        // Limiteer vraag lengte voor betere weergave
        $question = htmlspecialchars($row['question']);
        if (strlen($question) > 50) {
            $question = substr($question, 0, 50) . "...";
        }
        $table .= "<td>" . $question . "</td>";
        
        // Limiteer antwoord lengte
        $answer = htmlspecialchars($row['answer']);
        if (strlen($answer) > 30) {
            $answer = substr($answer, 0, 30) . "...";
        }
        $table .= "<td><strong>" . $answer . "</strong></td>";
        
        // Limiteer hint lengte
        $hint = htmlspecialchars($row['hint']);
        if (strlen($hint) > 40) {
            $hint = substr($hint, 0, 40) . "...";
        }
        $table .= "<td><em>" . $hint . "</em></td>";
        
        // Compactere kamer weergave - "K1" in plaats van "Kamer 1"
        $table .= "<td><span class='room-badge'>K" . htmlspecialchars($row['roomId']) . "</span></td>";
        
        if ($userRole == 'admin') {
            $table .= "<td>";
            $table .= "<button class='action-button edit-button' onclick=\"window.location.href='update_vragen.php?id=" . $row['id'] . "'\">✏️ Wijzig</button>";
            $table .= "<button class='action-button delete-button' onclick=\"confirmDelete(" . $row['id'] . ", '" . addslashes(htmlspecialchars($row['question'])) . "')\">🗑️ Delete</button>";
            $table .= "</td>";
        }
        
        $table .= "</tr>";
    }

    $table .= "</table>";
        
    echo $table;
}

function UpdateVragen($row) {
    try {
        // Connect database
        $conn = ConnectDb();

        // Update data uit de opgegeven table methode prepare
        $sql = "UPDATE questions SET question = :question, answer = :answer, hint = :hint, roomId = :roomId WHERE id = :id";

        $query = $conn->prepare($sql);
        $query->execute([
            ':question' => $row['question'],
            ':answer' => $row['answer'],
            ':hint' => $row['hint'],
            ':roomId' => $row['roomId'],
            ':id' => $row['id']
        ]);
        
        return true;
    }
    catch(PDOException $e) {
        echo "Update failed: " . $e->getMessage();
        return false;
    }
}

function DeleteVragen($id) {
    try {
        $conn = ConnectDb();

        $query = $conn->prepare("DELETE FROM questions WHERE id = :id");
        $query->execute([':id' => $id]);
        
        return true;
    } catch (PDOException $e) {
        echo "Delete failed: " . $e->getMessage();
        return false;
    }
}

function InsertVragen($post) {
    try {
        $conn = ConnectDb();

        $query = $conn->prepare("
        INSERT INTO questions (question, answer, hint, roomId)
        VALUES (:question, :answer, :hint, :roomId)");

        $query->execute([
            ':question' => $post['question'],
            ':answer' => $post['answer'],
            ':hint' => $post['hint'],
            ':roomId' => $post['roomId']
        ]);
        
        return true;
    } catch (PDOException $e) {
        echo "Insert failed: " . $e->getMessage();
        return false;
    }
}

// Functie om te controleren of gebruiker admin is
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Functie om te controleren of gebruiker is ingelogd
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>