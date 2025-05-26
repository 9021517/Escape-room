<?php
// Maak verbinding met je database
$conn = new mysqli("localhost", "root", "", "escape_room");
if ($conn->connect_error) {
  die("Verbinding mislukt: " . $conn->connect_error);
}

// Haal één willekeurige vraag op
$result = $conn->query("SELECT * FROM questions ORDER BY RAND() LIMIT 1");
$question = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Escape Room Game</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <section>
    <h1>Escape Room</h1>
    <p><strong>Vraag:</strong> <?= $question['question'] ?></p>
    <p><strong>Hint:</strong> <?= $question['hint'] ?></p>

    <form method="post">
      <input type="text" name="answer" placeholder="Typ je antwoord..." required>
      <input type="hidden" name="correct" value="<?= $question['answer'] ?>">
      <br><br>
      <button type="submit">Controleer</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $userAnswer = strtolower(trim($_POST['answer']));
      $correctAnswer = strtolower($_POST['correct']);

      if ($userAnswer === $correctAnswer) {
        echo "<p style='color: lightgreen;'>🎉 Goed gedaan! Je hebt het opgelost.</p>";
        // header("Location: win.html"); ← als je naar win-pagina wilt
      } else {
        echo "<p style='color: red;'>❌ Fout antwoord. Probeer opnieuw.</p>";
      }
    }
    ?>
  </section>
</body>
</html>
