<?php
session_start();

// Controleer of gebruiker is ingelogd en admin is
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: crud_vragen.php');
    exit;
}

require_once('functions_vragen.php');

// Verwerk het formulier
$message = '';
$messageType = '';

if(isset($_POST['send']) && !empty($_POST['question'])) {
    if(InsertVragen($_POST)) {
        $message = 'Vraag "' . htmlspecialchars($_POST['question']) . '" is succesvol toegevoegd!';
        $messageType = 'success';
    } else {
        $message = 'Er ging iets mis bij het toevoegen van de vraag.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Escape - Nieuwe Vraag Toevoegen</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background-color: black;
            background-image: url('homepage.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
        }

        .screen {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .content-box {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            color: white;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 0 15px rgba(255,255,255,0.2);
        }

        .content-box h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #4CAF50;
            text-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }

        .user-info {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .user-info span {
            font-weight: bold;
            color: #4CAF50;
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            color: white;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        input[type="text"], textarea, select {
            background: transparent;
            color: white;
            font-size: 16px;
            padding: 12px 15px;
            border: 2px solid white;
            width: 100%;
            font-family: 'Courier New', monospace;
            border-radius: 5px;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 0 5px rgba(255,255,255,0.3);
            box-sizing: border-box;
        }

        input[type="text"]:focus, textarea:focus, select:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
            transform: scale(1.02);
            border-color: #4CAF50;
        }

        input[type="text"]::placeholder, textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        select {
            cursor: pointer;
        }

        select option {
            background-color: #333;
            color: white;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        button, .button-link {
            background: transparent;
            color: white;
            font-size: 18px;
            padding: 12px 24px;
            border: 2px solid white;
            text-transform: uppercase;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 0 5px rgba(255,255,255,0.3);
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
        }

        button:hover, .button-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .submit-button {
            border-color: #4CAF50;
            color: #4CAF50;
        }

        .submit-button:hover {
            background-color: rgba(76, 175, 80, 0.1);
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }

        .back-button {
            border-color: #ff4444;
            color: #ff4444;
        }

        .back-button:hover {
            background-color: rgba(255, 68, 68, 0.1);
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
        }

        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .message.success {
            background-color: rgba(0, 255, 0, 0.2);
            color: #00ff00;
            border: 2px solid #00ff00;
        }

        .message.error {
            background-color: rgba(255, 0, 0, 0.3);
            color: #ff4444;
            border: 2px solid #ff4444;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .required {
            color: #ff4444;
        }

        .form-help {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 5px;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .content-box {
                padding: 20px;
            }
            
            .buttons {
                flex-direction: column;
                align-items: center;
            }
            
            button, .button-link {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <section class="screen">
        <div class="content-box">
            <h1>➕ Nieuwe Vraag Toevoegen</h1>
            
            <div class="user-info">
                Ingelogd als: <span><?php echo htmlspecialchars($_SESSION['username']); ?></span> (Administrator)
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
                <?php if ($messageType == 'success'): ?>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'crud_vragen.php';
                        }, 2000);
                    </script>
                <?php endif; ?>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-container">
                    <div class="form-group">
                        <label for="question">Vraag <span class="required">*</span></label>
                        <textarea name="question" id="question" required placeholder="Voer hier de vraag in..."></textarea>
                        <div class="form-help">Stel een duidelijke vraag die past bij de escape room</div>
                    </div>

                    <div class="form-group">
                        <label for="answer">Antwoord <span class="required">*</span></label>
                        <input type="text" name="answer" id="answer" required placeholder="Het juiste antwoord">
                        <div class="form-help">Het exacte antwoord dat de speler moet invoeren</div>
                    </div>

                    <div class="form-group">
                        <label for="hint">Hint <span class="required">*</span></label>
                        <textarea name="hint" id="hint" required placeholder="Een helpende hint voor de spelers..."></textarea>
                        <div class="form-help">Een hint die helpt zonder het antwoord weg te geven</div>
                    </div>

                    <div class="form-group">
                        <label for="roomId">Kamer <span class="required">*</span></label>
                        <select name="roomId" id="roomId" required>
                            <option value="">Selecteer een kamer</option>
                            <option value="1">Kamer 1 - Horror Room</option>
                            <option value="2">Kamer 2 - Tech Room</option>
                            <option value="3">Kamer 3 - Final Room</option>
                        </select>
                        <div class="form-help">In welke kamer van de escape room hoort deze vraag?</div>
                    </div>
                </div>

                <div class="buttons">
                    <button type="submit" name="send" class="submit-button">💾 Vraag Toevoegen</button>
                    <a href="crud_vragen.php" class="button-link back-button">🔙 Terug naar Overzicht</a>
                </div>
            </form>
        </div>
    </section>

    <script>
        // Auto-resize textarea
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const question = document.getElementById('question').value.trim();
            const answer = document.getElementById('answer').value.trim();
            const hint = document.getElementById('hint').value.trim();
            const roomId = document.getElementById('roomId').value;

            if (!question || !answer || !hint || !roomId) {
                e.preventDefault();
                alert('Vul alle verplichte velden in!');
                return;
            }

            if (question.length < 10) {
                e.preventDefault();
                alert('De vraag moet minimaal 10 karakters lang zijn.');
                return;
            }

            if (answer.length < 2) {
                e.preventDefault();
                alert('Het antwoord moet minimaal 2 karakters lang zijn.');
                return;
            }
        });
    </script>
</body>
</html>