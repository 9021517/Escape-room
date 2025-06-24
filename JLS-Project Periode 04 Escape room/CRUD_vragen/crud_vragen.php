<?php 
session_start();

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: ../indexvit.html');
    exit;
}

// functie: Programma CRUD vragen
// auteur: Ivanov

// Initialisatie
include 'functions_vragen.php';

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Escape - Vragen Beheer</title>
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
            overflow-y: auto;
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
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            color: white;
            max-width: 1200px;
            width: 100%;
            box-shadow: 0 0 15px rgba(255,255,255,0.2);
        }

        .content-box h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: white;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .user-info {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-size: 18px;
        }

        .user-info span {
            font-weight: bold;
            color: #00ff00;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .nav-buttons a, .nav-buttons button {
            background: transparent;
            color: white;
            font-size: 18px;
            padding: 12px 24px;
            border: 2px solid white;
            text-transform: uppercase;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 0 5px rgba(255,255,255,0.3);
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            width: 250px;
        }

        .nav-buttons a:hover, .nav-buttons button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .nav-buttons .admin-only {
            border-color: #4CAF50;
            color: #4CAF50;
        }

        .nav-buttons .admin-only:hover {
            background-color: rgba(76, 175, 80, 0.1);
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }

        .nav-buttons .logout {
            border-color: #ff4444;
            color: #ff4444;
        }

        .nav-buttons .logout:hover {
            background-color: rgba(255, 68, 68, 0.1);
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
        }

        .questions-table {
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
            padding: 20px;
            overflow-x: auto;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 15px rgba(255,255,255,0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
            font-family: 'Courier New', monospace;
            table-layout: auto;
            min-width: 800px;
        }

        th {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 15px;
            text-align: left;
            border: 2px solid white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 16px;
        }

        td {
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background-color: rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .action-button {
            background: transparent;
            color: white;
            font-size: 12px;
            padding: 6px 10px;
            border: 2px solid white;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            transition: all 0.2s ease-in-out;
            margin: 1px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 0 5px rgba(255,255,255,0.3);
            white-space: nowrap;
        }

        .action-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .edit-button {
            border-color: #4CAF50;
            color: #4CAF50;
        }

        .edit-button:hover {
            background-color: rgba(76, 175, 80, 0.1);
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }

        .delete-button {
            border-color: #ff4444;
            color: #ff4444;
        }

        .delete-button:hover {
            background-color: rgba(255, 68, 68, 0.1);
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
        }

        .room-badge {
            background-color: rgba(0, 255, 255, 0.2);
            color: #00ffff;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            border: 1px solid #00ffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            box-shadow: 0 0 3px rgba(0, 255, 255, 0.3);
            white-space: nowrap;
            display: inline-block;
        }

        /* Maak kolommen smaller */
        th:nth-child(5), td:nth-child(5) {
            width: 80px;
            text-align: center;
            min-width: 80px;
        }

        th:nth-child(6), td:nth-child(6) {
            width: 180px;
            text-align: center;
            min-width: 180px;
        }

        .no-data {
            color: #ff4444;
            font-size: 18px;
            padding: 40px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .room-badge {
                font-size: 10px;
                padding: 3px 6px;
            }
            
            .action-button {
                font-size: 11px;
                padding: 5px 8px;
            }
            
            th:nth-child(5), td:nth-child(5) {
                width: 70px;
                min-width: 70px;
            }
            
            th:nth-child(6), td:nth-child(6) {
                width: 160px;
                min-width: 160px;
            }
        }

        @media (max-width: 768px) {
            .content-box {
                padding: 20px;
                margin: 10px;
            }
            
            .content-box h1 {
                font-size: 28px;
            }
            
            .nav-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .nav-buttons a, .nav-buttons button {
                width: 100%;
                max-width: 300px;
                font-size: 16px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 8px;
            }
            
            .room-badge {
                font-size: 9px;
                padding: 2px 4px;
                letter-spacing: 0;
            }
            
            .action-button {
                font-size: 10px;
                padding: 4px 6px;
                margin: 0.5px;
            }
            
            th:nth-child(5), td:nth-child(5) {
                width: 60px;
                min-width: 60px;
            }
            
            th:nth-child(6), td:nth-child(6) {
                width: 140px;
                min-width: 140px;
            }
        }

        /* Animaties */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content-box {
            animation: fadeIn 0.5s ease-in-out;
        }

        .questions-table {
            animation: fadeIn 0.7s ease-in-out;
        }

        /* Scroll styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <section class="screen">
        <div class="content-box">
            <br><br><br><br><br><br><br><br><br><br>
            <h1>Hospital Escape - Vragen Beheer</h1>
            
            <div class="user-info">
                Ingelogd als: <span><?php echo htmlspecialchars($_SESSION['username']); ?></span> 
                (<?php echo $_SESSION['role'] == 'admin' ? 'Administrator' : 'Speler'; ?>)
            </div>

            <div class="nav-buttons">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href='insert_vragen.php' class="admin-only">Nieuwe Vraag</a>
                <?php endif; ?>
                <a href='../homepagina/indexvit.html'>Terug naar Home</a>
                <button onclick="logout()" class="logout">Uitloggen</button>
            </div>

            <div class="questions-table">
                <?php 
                // Print overzicht vragen
                CrudVragen($_SESSION['role']); 
                ?>
            </div>
        </div>
    </section>

    <script>
        function logout() {
            if (confirm('Weet je zeker dat je wilt uitloggen?')) {
                fetch('../logout.php', {
                    method: 'POST'
                }).then(() => {
                    window.location.href = '../homepagina/indexvit.html';
                });
            }
        }

        function confirmDelete(id, question) {
            if (confirm(`Weet je zeker dat je deze vraag wilt verwijderen?\n\n"${question}"`)) {
                window.location.href = `delete_vragen.php?id=${id}`;
            }
        }

        // Fade-in effect voor table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.style.animation = `fadeIn 0.5s ease-in-out ${index * 0.1}s both`;
            });
        });
    </script>
</body>
</html>