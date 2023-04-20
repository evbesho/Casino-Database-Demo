<html>
    <head>
        <title>Manage Game Sessions and Players</title>
    </head>

    <?php
        require './db_connection.php';

        function patrons() {
            if(isset($_POST['gameSession'])) {
                if(connectToDB()) {
                    $sql = "SELECT p.patronID, p.name, p.homeAddress FROM Patron p, PatronPlays plays WHERE p.patronID = plays.patronID AND plays.sessionID = :bind1";

                    $tuple = array (
                        ":bind1" => intval($_POST['gameSession'])
                    );
    
                    $alltuples = array (
                        $tuple
                    );

                    $result = executeBoundSQL($sql, $alltuples);

                    while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        // echo "<option value='".$row['SESSIONID']."'>".$row['SESSIONID']."</option>";
                        echo "<label for=".$row['PATRONID'].">".$row['NAME'] . " (" . $row['HOMEADDRESS'] . ")</label>";
                        echo ": ";
                        echo "<input type=number name=" . $row['PATRONID'] . " required>";
                        echo "<br>";
                    }

                    disconnectFromDB();    
                }
            } else {
                if(!isset($_POST['endGameFinal'])) {
                    header("Location: gameSessionManagement.php");
                }
            }
        }
    ?>

    <body>
        <h2>End Gameplay Session</h2>
        <form method="POST" action="endGameSession.php"> <!--refresh page when submitted-->
            <input type="hidden" id="endGameFinal" name="endGameFinal">
            <h5>Players and Earnings:</h5>
            <?php echo "<input type=hidden id=gameSession name=gameSession value=".$_POST['gameSession'].">"; ?>
            <?php patrons(); ?>
            <br>
            <input type="submit" value="End Session" name="endGameFinalSubmit"></p>
        </form>
        <hr />
    </body>

        <?php
        function submitEndGame() {
            global $db_conn;
            if(connectToDB()) {
                // for each player, update PatronPlays with the gained ammount
                $sql = "SELECT p.patronID FROM Patron p, PatronPlays plays WHERE p.patronID = plays.patronID AND plays.sessionID = :bind1";

                $sessionID = intval($_POST['gameSession']);
                $tuple = array (
                    ":bind1" => $sessionID
                );

                $alltuples = array (
                    $tuple
                );

                $result = executeBoundSQL($sql, $alltuples);

                $casinoGain = 0;

                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    $earnings = $_POST[intval($row['PATRONID'])];

                    $updateSQL = "UPDATE PatronPlays SET playerGain = :bind1 WHERE patronID = :bind2 AND sessionID = :bind3";

                    $updateTuple = array (
                        ":bind1" => $earnings,
                        ":bind2" => $row['PATRONID'],
                        ":bind3" => $sessionID
                    );
    
                    $updateTuples = array (
                        $updateTuple
                    );

                    executeBoundSQL($updateSQL, $updateTuples);
                    OCICommit($db_conn);

                    $casinoGain -= intval($earnings);
                }

                $date = date("d-M-Y H:i:s");
                $dateSQL = "UPDATE GameSession SET endTime=TO_DATE(:bind1, 'DD-MON-YYYY hh24:mi:ss'), casinoGain=:bind3 WHERE sessionID = :bind2";
                $tuple = array (
                    ":bind1" => $date,
                    ":bind2" => $sessionID,
                    ":bind3" => $casinoGain
                );

                $alltuples = array (
                    $tuple
                );

                executeBoundSQL($dateSQL, $alltuples);
                OCICommit($db_conn);

                disconnectFromDB();

                echo '<script type="text/javascript">' .
                ' alert("Session Ended successfully!");' .
                ' window.location.href = "gameSessionManagement.php";' .
                '</script>';
            }
            // Set the game session's end time to be the current time
            // Set the game session's casinoGain to be negative player gain
        }
        
        function handlePOSTRequest() {
            if (connectToDB()) { 
                if (array_key_exists('endGameFinal', $_POST)) {
                    submitEndGame();
                } else if (array_key_exists('endGameSubmit', $_POST)) {

                }
                disconnectFromDB();
            }
        }

        if (isset($_POST['endGameFinalSubmit']) || isset($_POST['endGameSubmit'])) {
            handlePOSTRequest();
        }
        ?>
</html>
