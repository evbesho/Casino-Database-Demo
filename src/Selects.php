<html>

    <head>
        <title>Audit Casino Branches</title>
    </head>

    <body>
    <h2>Top Dealer Employees! (dealers who worked in every branch)</h2>
            <form method="POST" action="Selects.php">
                <input type="hidden" id="dealerDivisionRequest" name="dealerDivisionRequest">
                <input type="submit" value="View" name="dealerDivision"></p>
        </form>
        <hr />
        <h2>Branches to Shut Down (branches which lost more than all the players' gains combined across all sessions)</h2>
            <form method="POST" action="Selects.php">
                <input type="hidden" id="casinoLossRequest" name="casinoLossRequest">
                <input type="submit" value="View" name="casinoLoss"></p>
        </form>
        <hr />
        <h2>View Job Assignments For a Chosen Dealer</h2>
            <form method="POST" action="Selects.php">
                <input type="hidden" id="dealerAssignsRequest" name="dealerAssignsRequest">
                <?php
                require './db_connection.php';

                // dropdown of dealers
                function dealerDropdown() {
                    if(connectToDB()) {
                        echo '<label for="dealerDropdown">Dealer:</label>';
                        echo '<select name="dealerDropdown" id="dealerDropdown">';
                        $result = executePlainSQL("SELECT dealerID, name FROM Dealer");
                        while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                            echo "<option value='".$row['DEALERID']."'>" . $row['DEALERID'].": ".$row['NAME']."</option>";
                        }
                        disconnectFromDB();
                        echo '</select>';
                    } else {
                        sendAlert("Database connection failed.");
                    }
                }

                dealerDropdown();
                ?>
                <input type="submit" value="View" name="dealerAssigns"></p>
        </form>
        <hr />

        <h2>View Dealers who have Earned the Casino Money</h2>
        <form method="POST" action="Selects.php">
            <input type="hidden" id="proffitable" name="proffitable">
            <input type="submit" value="View" name="proffitableSubmit"></p>
        </form>
        <hr />

        <h2>View Information About an Area of the Casino</h2>
            <form method="POST" action="Projection.php">
                <input type="hidden" id="projectionRequest" name="projectionRequest">
                <label for="tablesDropdown">All Areas of Interest: </label>
                <select name="tablesDropdown" id="tablesDropdown">
                <?php 

                // array of tupes, where each tuple has the name of the table in the DB, and the label we show the user
                $tables = array(array("CasinoBranch","Casino Branches"), 
                                array("Room", "Rooms"), 
                                array("Dealer","Dealers"), 
                                array("CasinoTable","Casino Tables"), 
                                array("Game","Games"), 
                                array("Party","Parties"), 
                                array("Patron","Patrons"), 
                                array("GameSession","Game Sessions"), 
                                array("CasinoServes","Casinos & Parties"), 
                                array("PatronPlays","Patrons & Game Sessions"));

                    // create a dropdown of the table names
                    function tablesDropdown() {
                        global $tables;
                        foreach($tables as $table) {
                            echo '<option value="'.$table[0].'">'.$table[1].'</option>';
                        }
                    }
                    tablesDropdown();
                ?>
                <input type="submit" value="View" name="table"></p>
        </form>
        <hr />

        <h2>View All Sessions of a Chosen Game</h2>
            <form method="POST" action="Selects.php">
                <input type="hidden" id="gameSessionsRequest" name="gameSessionsRequest">
                <?php
                // dropdown of dealers
                function gameDropdown() {
                    if(connectToDB()) {
                        echo '<label for="gameDropdown">Games:</label>';
                        echo '<select name="gameDropdown" id="gameDropdown">';
                        $result = executePlainSQL("SELECT name, variant, numplayers FROM Game");
                        while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                            echo "<option value='".$row['NAME'].",".$row['VARIANT']."'>" .
                                 $row['NAME'] . ": " . $row['VARIANT'] . " (" . $row['NUMPLAYERS'] . " Players)" ."</option>";
                        }
                        disconnectFromDB();
                        echo '</select>';
                    } else {
                        sendAlert("Database connection failed.");
                    }
                }

                gameDropdown();
                ?>
                <input type="submit" value="View" name="gameSessions"></p>
        </form>
        <hr />

        <?php

            // gets dealers who worked in every branch 
            // division query
            function getDealerDivision() {
                global $db_conn;
                $result = executePlainSQL("SELECT d.dealerId, d.name FROM Dealer d WHERE NOT EXISTS (
                                            (SELECT DISTINCT cb.name
                                                FROM CasinoBranch cb)
                                            MINUS
                                            (SELECT DISTINCT gs.branchName
                                                FROM GameSession gs
                                                WHERE gs.dealerId = d.dealerID))");

                echo "<table>";
                echo "<tr><th>Dealer ID</th><th>Dealer Name</th></tr>";

                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<tr><td>" . $row["DEALERID"] . "</td><td>" . $row["NAME"] . "</td></tr>";
                }
                echo "</table>";
            }

            // gets the losses of every branch across all game sessions. Only if the casino actually lost money
            // nested aggregation query
            function casinoLosses() {
                global $db_conn;
                executePlainSQL("create view PlayerGains(branchName, gains) as
                                    SELECT gs.branchName, SUM(p.playerGain)
                                        FROM PatronPlays p, GameSession gs
                                        WHERE gs.sessionId = p.sessionId
                                        GROUP BY gs.branchName");
                OCICommit($db_conn);
                $result = executePlainSQL("SELECT gs1.branchName, (SELECT gains from PlayerGains where PlayerGains.branchName = gs1.branchName) - SUM(casinoGain) as TotalLoss
                                            FROM GameSession gs1
                                            GROUP BY gs1.branchName
                                            HAVING SUM(casinoGain) < (SELECT gains from PlayerGains where PlayerGains.branchName = gs1.branchName)
                                            ORDER BY TotalLoss desc");

                echo "<table>";
                echo "<tr><th>Casino Branch</th><th>Total Loss (in dollars)</th></tr>";

                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<tr><td>" . $row["BRANCHNAME"] . "</td><td>" . $row["TOTALLOSS"] . "</td></tr>";
                }
                echo "</table>";

                executePlainSQL("drop view PlayerGains");
                OCICommit($db_conn);

            }

            // gets assignments for a dealer across sessions
            // JOIN query
            function dealerAssignments() {
                $tuple = array(":bind1" => $_POST['dealerDropdown']);
                $alltuples = array($tuple);
                $result = executeBoundSQL("SELECT gs.sessionid as ID, gs.branchName, gs.tableNum, TO_CHAR(gs.startTime, 'DD-MON-YYYY HH24:MI:SS') as starttime, TO_CHAR(gs.endTime, 'DD-MON-YYYY HH24:MI:SS') as endtime, gs.gameName, gs.gameVariant
                                            FROM Dealer d, GameSession gs
                                            WHERE gs.dealerId=d.dealerId and d.dealerId=:bind1", $alltuples);
                echo "<table>";
                echo "<tr><th>Session ID</th><th>Casino Branch</th><th>Table Number</th><th>Game Name</th><th>Game Variant</th><th>Start Time</th><th>End Time</th></tr>";
                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<tr><td>" .$row["ID"]."</td><td>".$row["BRANCHNAME"] . "</td><td>" . $row["TABLENUM"] . "</td><td>". $row["GAMENAME"]. "</td><td>".$row["GAMEVARIANT"]."</td><td>".
                            $row["STARTTIME"]."</td><td>".$row["ENDTIME"]."</td></tr>";
                }
                echo '</table>';
            }

            function viewDealersEarnedMoney() {
                global $db_conn;
                $result = array();

                $result = executePlainSQL("SELECT d.dealerID, d.name, SUM(gs.casinoGain) as gain FROM Dealer d, GameSession gs " .
                                          "WHERE d.dealerID = gs.dealerID GROUP BY d.dealerID, d.name HAVING SUM(gs.casinoGain) > 0");

                echo "<table>" . "<tr> <th>Dealer ID</th> <th>Dealer Name</th> <th>Total Earned</th> </tr>";
                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    // echo "<option value='".$row['SESSIONID']."'>".$row['SESSIONID']."</option>";
                    echo "<tr><td>" . $row['DEALERID'] . "</td><td>" . $row['NAME'] . "</td><td>" . $row['GAIN'] . "</td></tr>";
                }
                echo "</table>";
            }

            function gameSessionsForGame() {
                $gameData = explode(",", $_POST['gameDropdown']);
                $game = $gameData[0];
                $variant = $gameData[1];

                $tuple = array(":bind1" => $game, ":bind2" => $variant);
                $alltuples = array($tuple);
                $result = executeBoundSQL("SELECT sessionID, TO_CHAR(startTime, 'DD-MON-YYYY HH24:MI:SS') AS startTime, TO_CHAR(endTime, 'DD-MON-YYYY HH24:MI:SS') as endTime, gameName, gameVariant, dealerID, branchName, tableNum, casinoGain FROM GameSession WHERE gameName = :bind1 AND gameVariant = :bind2", $alltuples);
                echo "<table>";
                echo "<tr><th>Session ID</th><th>Start Time</th><th>EndTime</th><th>Game Name</th><th>Game Variant</th><th>Dealer ID</th><th>Branch Name</th><th>Table Number</th><th>Casino Gain</th></tr>";
                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<tr>".
                        "<td>".$row['SESSIONID']."</td>".
                        "<td>".$row['STARTTIME']."</td>".
                        "<td>".$row['ENDTIME']."</td>".
                        "<td>".$row['GAMENAME']."</td>".
                        "<td>".$row['GAMEVARIANT']."</td>".
                        "<td>".$row['DEALERID']."</td>".
                        "<td>".$row['BRANCHNAME']."</td>".
                        "<td>".$row['TABLENUM']."</td>".
                        "<td>".$row['CASINOGAIN']."</td>".
                        "</tr>";
                }
                echo '</table>';
            }

            function handlePostRequest() {
                if (connectToDB()) {
                    if (array_key_exists('dealerAssignsRequest', $_POST)) {
                        echo "<h2>Game Sessions Assigned to Selected Dealer:</h2>";
                        dealerAssignments();
                    } else if (array_key_exists('proffitableSubmit', $_POST)) {
                        echo "<h2>Dealers Who Have Earned Proffits:</h2>";
                        viewDealersEarnedMoney();
                    } else if (array_key_exists('dealerDivisionRequest', $_POST)) {
                        echo "<h2>Top Dealers! Who have worked at every branch:</h2>";
                        getDealerDivision();
                    } else if (array_key_exists('casinoLossRequest', $_POST)) {
                        echo "<h2>Casinos Branches that have Lost Money:</h2>";
                        casinoLosses();
                    } else if (array_key_exists('gameSessions', $_POST)) {
                        echo "<h2>Game Sessions of the Selected Game:</h2>";
                        gameSessionsForGame();
                    }
                    disconnectFromDB();

                    echo "<br /><hr />";
                } else {
                    sendAlert("POST Request: Database connection failed.");
                }
            }

            if (isset($_POST['dealerAssigns']) || isset($_POST['dealerDivision']) || isset($_POST['casinoLoss']) || isset($_POST['proffitableSubmit']) || isset($_POST['gameSessions'])) {
                handlePostRequest();
            }

        ?>
    </body>

    <footer>
        <form method="POST" action="Home.php">
                <input type="submit" value="Go Back" name="home"></p>
        </form>
        </footer>

</html>
