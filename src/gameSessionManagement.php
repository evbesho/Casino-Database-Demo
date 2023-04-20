<html>
    <head>
        <title>Manage Game Sessions and Players</title>
    </head>

    <?php
        require './db_connection.php';

        function gameDropdown() {
            if(connectToDB()) {
                $result = array();
                $result = executePlainSQL("SELECT name, variant FROM Game");
                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<option value='".$row['NAME'].",".$row['VARIANT']."'>" . $row['NAME']." - ".$row['VARIANT']."</option>";
                }
                disconnectFromDB();
            } else {
                sendAlert("Game Dropdown: Failed to connect to database.");
            }
        }

        function dealerDropdown() {
            if(connectToDB()) {
                $result = array();
                $result = executePlainSQL("SELECT d.dealerID, d.name FROM Dealer d WHERE NOT EXISTS " .
                                          "(SELECT * FROM GameSession gs WHERE gs.endTime IS NULL AND gs.dealerID = d.dealerID) " .
                                          "ORDER BY d.dealerID ASC");
                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<option value='".$row['DEALERID']."'>" . $row['DEALERID'].": ".$row['NAME']."</option>";
                }
                disconnectFromDB();
            } else {
                sendAlert("Dealer Dropdown: Failed to connect to database.");
            }
        }

        function tableDropdown() {
            if(connectToDB()) {
                $result = array();
                $result = executePlainSQL("SELECT c.tableNum, c.capacity, c.branchName FROM CasinoTable c WHERE NOT EXISTS " .
                                          "(SELECT * FROM GameSession gs WHERE gs.endTime IS NULL AND gs.tableNum = c.tableNum AND gs.branchName = c.branchName) " .
                                          "ORDER BY c.branchName, c.tableNum ASC");
                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<option value='".$row['BRANCHNAME'].",".$row['TABLENUM']."'>" . $row['BRANCHNAME']." ".$row['TABLENUM'].": ".$row['CAPACITY']."</option>";
                }
                disconnectFromDB();
            } else {
                sendAlert("Table Dropdown: Failed to connect to database.");
            }
        }

        function gameSessions() {
            if(connectToDB()) {
                $result = array();

                $result = executePlainSQL("SELECT sessionID, gameName, gameVariant FROM GameSession WHERE endTime IS NULL");
                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<option value='".$row['SESSIONID']."'>".$row['SESSIONID']." (".$row['GAMENAME'].": ".$row['GAMEVARIANT'].")"."</option>";
                }
                disconnectFromDB();
            } else {
                sendAlert("Game Sessions: Failed to connect to database.");
            }
        }

        function patronDropdown() {
            if (connectToDB()) {
                $result = executePlainSQL("SELECT p.patronID, p.name, p.homeAddress FROM Patron p WHERE NOT EXISTS " .
                "(SELECT * FROM PatronPlays pp, GameSession g WHERE pp.sessionID = g.sessionID AND pp.patronID = p.patronID AND g.endTime IS NULL)");
                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<option value='".$row['PATRONID']."'>" . $row['NAME'] . " (" . $row['HOMEADDRESS']. ")" . '</option>';
                }
                disconnectFromDB();
            } else {
                sendAlert("Patron Dropdown: Failed to connect to database.");
            }
        }
    ?>

    <body>
        <h2>Start Gameplay Session</h2>
        <form method="POST" action="gameSessionManagement.php">
            <input type="hidden" id="startGameRequest" name="startGameRequest">
            <label for="gameDropdown">Game</label>
            <select name="gameDropdown" id="gameDropdown">
                <?php gameDropdown(); ?>
            </select>
            <br>
            <label for="dealerDropdown">Dealer:</label>
            <select name="dealerDropdown" id="dealerDropdown">
                <?php dealerDropdown(); ?>
            </select>
            <br>
            <label for="tableDropdown">Table:</label>
            <select name="tableDropdown" id="tableDropdown">
                <?php tableDropdown(); ?>
            </select>
            <br>
            <input type="submit" value="Add" name="startGameSubmit"></p>
        </form>
        <hr />

        <h2>Add Player To Session</h2>
        <form method="POST" action="gameSessionManagement.php">
            <input type="hidden" id="addPlayerRequest" name="addPlayerRequest">
            <label for="gameSession">Game Session:</label>
            <select name="gameSession" id="gameSession">
                <?php gameSessions(); ?>
            </select>
            <br>
            <label for="playerName">Player:</label>
            <select name="playerName" id="playerName">
                <?php patronDropdown(); ?>
            </select>
            <br>
            <input type="submit" value="Add" name="addPlayerSubmit"></p>
        </form>
        <hr />

        <h2>End Game Session</h2>
        <form method="POST" action="endGameSession.php">
            <input type="hidden" id="endSession" name="endSession">
            <label for="gameSession">Game Session:</label>
            <select name="gameSession" id="gameSession">
                <?php gameSessions(); ?>
            </select>
            <br>
            <input type="submit" value="Enter Earnings and Loss" name="endGameSubmit"></p>
        </form>
        <hr />
    </body>

        <?php
        function startGameSession() {
            global $db_conn;

            if(!array_key_exists('gameDropdown', $_POST) || !array_key_exists('dealerDropdown', $_POST) || !array_key_exists('tableDropdown', $_POST)) {
                sendAlert("Not all fields set, request failed.");
                return;
            }

            $date = date("d-M-Y H:i:s");
            $gameData = explode(",", $_POST['gameDropdown']);
            $game = $gameData[0];
            $variant = $gameData[1];

            $dealer = $_POST['dealerDropdown'];

            $tableData = explode(",", $_POST['tableDropdown']);
            $branchName = $tableData[0];
            $tableNum = $tableData[1];

            $tuple = array (
                ":bind1" => $date,
                ":bind2" => $game,
                ":bind3" => $variant,
                ":bind4" => intval($dealer),
                ":bind5" => $branchName,
                ":bind6" => intval($tableNum)
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("INSERT INTO GameSession (sessionID, startTime, endTime, gameName, gameVariant, dealerID, branchName, tableNum, casinoGain) VALUES"
             ."(session_id.nextval, TO_DATE(:bind1, 'DD-MON-YYYY hh24:mi:ss'), NULL, :bind2, :bind3, :bind4, :bind5, :bind6, 0)", $alltuples);

            OCICommit($db_conn);

            sendAlert("Game Session Created successfully.");
            echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
        }

        function addPlayerToGame() {
            global $db_conn;


            if(!array_key_exists('gameSession', $_POST) || !array_key_exists('playerName', $_POST)) {
                sendAlert("Not all fields set, request failed.");
                return;
            }

            $tuple = array (
                ":bind1" => $_POST['gameSession'],
                ":bind2" => $_POST['playerName']
            );

            $alltuples = array (
                $tuple
            );

            $sql = "INSERT INTO PatronPlays(sessionID, patronID, playerGain) VALUES(:bind1, :bind2, 0)";

            executeBoundSQL($sql, $alltuples);
            OCICommit($db_conn);

            sendAlert("Player added to game session.");
            echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
        }

        function handlePOSTRequest() {
            if (connectToDB()) { 
                if (array_key_exists('startGameRequest', $_POST)) {
                    startGameSession();
                } else if (array_key_exists('addPlayerRequest', $_POST)) {
                    addPlayerToGame();
                }
                disconnectFromDB();
            } else {
                sendAlert("Failed to connect to the database while handling a POST request.");
            }
        }

        if (isset($_POST['startGameSubmit']) || isset($_POST['addPlayerSubmit']) || isset($_POST['endGameSubmit'])) {
            handlePOSTRequest();
        }
        ?>

        <footer>
        <form method="POST" action="Home.php">
                <input type="submit" value="Go Back" name="home"></p>
        </form>
        </footer>
</html>
