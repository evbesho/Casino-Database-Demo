<html>

    <head>
        <title>Create Patrons and Parties</title>

        <style>
            <?php
            $css = file_get_contents('styles.css');
            echo $css;
            ?>
        </style>

    </head>

    <body>
        <h1>Party Patron Management</h1>

        <form method="POST" action="partyPatron.php">
            <h2>Create New Party</h2>
            <input class="input_field" type="hidden" id="insertPartyRequest" name="insertPartyRequest">
            Initial Account Balance: <input class="input_field" type="text" name="insBalance" required>
            <div></div>
            <input class="button" type="submit" value="Add" name="partySubmit">
        </form>


        <form method="POST" action="partyPatron.php">
            <h2>Add New Patron</h2>
            <input class="input_field" type="hidden" id="insertPatronRequest" name="insertPatronRequest">
            Name: <input class="input_field" type="text" name="insName" required>
            <div></div>
            Home Address: <input class="input_field" type="text" name="insAddr" required>
            <div></div>
            <label for="getPartyIds">Available Parties (Required):</label>
            <select class="input_field" name="getPartyIds" id="getPartyIds">
         <?php
            require './db_connection.php';

            $partyIds = array();
            
            // creates a dropdown with the existing party ids
            function getPartyIds() {
                global $partyIds;
                if (connectToDB()) {
                    $result = executePlainSQL("select partyid from Party");
                    while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        array_push($partyIds, $row['PARTYID']);
                    }
                    disconnectFromDB();
                }
            }

            // dropdown of patrons and parties for choosing when adding a new patron
            function partyAndPatronsDropDown() {
                global $partyIds;
                if (connectToDB()) {
                    for ($i = 0; $i < sizeof($partyIds); $i++) {
                        $curr = $partyIds[$i];
                        $patrons = executePlainSQL("select name, homeAddress from Patron where partyId=".$curr);
                        $option = "<option>".$curr . ": ";
                        while ($patron = OCI_Fetch_Array($patrons, OCI_BOTH)) {
                            $option = $option. $patron['NAME'] . " (" . $patron['HOMEADDRESS'] . "), ";
                        }
                        // $option = substr($option, 0, -2);
                        $partyInfo = executePlainSQL("select accountnum, balance, roomnum, partysize, startdate, enddate
                                                        from party where partyid=$curr");
                        $row = OCI_Fetch_Array($partyInfo, OCI_BOTH);
                        echo $option . '; Account No.: ' .$row['ACCOUNTNUM'].'; Balance: $ '.$row['BALANCE']. '; Room Numer: '. $row['ROOMNUM']. 
                                '; Stay Start Date: ' .$row['STARTDATE'].'; Stay End Date: '. $row['ENDDATE']. '; Size: ' .$row['PARTYSIZE']. "</option>";
                    }
                    
                    disconnectFromDB();
                }
            }

            getPartyIds();	
            partyAndPatronsDropDown();
         ?>
         </select>
        <div></div>
        <input class="button" type="submit" value="Add" name="patronSubmit">
        </form>


        <form method="POST" action="partyPatron.php">
            <h2>Remove a Patron</h2>
            <input class="input_field" type="hidden" id="deletePatronRequest" name="deletePatronRequest">
            <label for="patronNameAddrDropdown">Patrons:</label>
            <select class="input_field" name="patronNameAddrDropdown" id="patronNameAddrDropdown">
         <?php

            $patronNameAddrs = array();

            // creates a dropdown with all existing patrons' name and home address, which can be used as a key
            function patronNameAddrDropdown() {
                global $patronNameAddrs;
                if (connectToDB()) {
                    $result = executePlainSQL("select * from Patron");
                    while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        array_push($patronNameAddrs, array($row['NAME'], $row['HOMEADDRESS']));
                        echo '<option>'. $row['PATRONID'].': '.$row['NAME']. ' ('. $row['HOMEADDRESS'] . '), Party: '. $row['PARTYID']. '</option>';
                    }
                    disconnectFromDB();
                }
            }
            patronNameAddrDropdown();
         ?>
         </select>
        <div></div>
        <input class="button" type="submit" value="Delete" name="patronDelete">
        </form>


        <form method="POST" action="partyPatron.php">
            <h2>Update Information For a Patron</h2>
            <input class="input_field" type="hidden" id="updatePatronRequest" name="updatePatronRequest">
             <label for="patronDropdownUpdate">Patrons:</label>
             <select class="input_field" name="patronDropdownUpdate" id="patronDropdownUpdate">
         <?php 

            // dropdown for patrons to choose for the update query
            function updatePatronDropdown() {
                if (connectToDB()) {
                    $result = executePlainSQL("select * from Patron");
                    while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo '<option>'. $row['PATRONID'].': '.$row['NAME']. ' ('. $row['HOMEADDRESS'] . '), Party: '. $row['PARTYID']. '</option>';
                    }
                    disconnectFromDB();
                }

            }
            updatePatronDropdown();
         
         ?>
             </select>
            <div></div>
            Name: <input class="input_field" type="text" name="updateName">
            <div></div>
            Home Address: <input class="input_field" type="text" name="updateAddr">
            <div></div>
            <label for="partyIdDropdown">Available Parties IDs:</label>
            <select class="input_field" name="partyIdDropdown" id="partyIdDropdown">
        <?php 
        
            // creates dropdown of party ids
            function partyIdDropdown() {
                global $partyIds;
                foreach($partyIds as $id) {
                    echo "<option>".$id."</option>";
                }
            }
            partyIdDropdown();
        
        ?>
            </select>
            <div></div>
            <input class="button" type="submit" value="Update" name="patronUpdate">
        </form>


        <form method="POST" action="partyPatron.php">
            <h2>Remove a Party and All Its Patrons</h2>
            <input class="input_field" type="hidden" id="deletePartyRequest" name="deletePartyRequest">
            <label for="partyAndPatronsDropdown">Parties:</label>
            <select class="input_field" name="partyAndPatronsDropdown" id="partyAndPatronsDropdown">
         <?php

            partyAndPatronsDropDown();
            
         ?>
             </select>
            <div></div>
            <input class="button" type="submit" value="Delete" name="partyDelete"></p>
        </form>


        <form method="GET" action="partyPatron.php">
            <h2>All Patron Gains Across All Sessions</h2>
            <input class="input_field" type="hidden" id="gainsRequest" name="gainsRequest">
            <input class="button" type="submit" value="View" name="gains">
        </form>
        <?php

        // inserts a new party w/ a given balance into the Party table
        function createParty() {
            if (is_numeric($_POST['insBalance'])) {
                global $db_conn;
                $tuple = array(
                    ":bind1" => $_POST['insBalance']
                );
                $alltuples = array($tuple);
                executeBoundSQL("insert into Party(partyId, accountNum, balance) values (party_id.nextval, account_id.nextval,:bind1)", $alltuples);
                OCICommit($db_conn);
                sendAlert("Party successfuly created! It can be viewed at the party dropdown below");
                echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
            } else {
                sendAlert("Balance must be an integer value");
            }
            
        }

        // inserts a new patron w/ given given name, home address and chosen partyId into Patron table, 
        // and updates size of in Party table
        function createPatron() {
            global $db_conn, $success, $partyIds;
            $curr = strtok($_POST['getPartyIds'], ":");
            for ($i = 0; $i < sizeof($partyIds); $i++) {
                if (str_contains($curr, $partyIds[$i])) {
                    $tuple = array(
                        ":bind1" => $_POST['insName'],
                        ":bind2" => $_POST['insAddr'],
                        ":bind3" => $partyIds[$i]
                    );
                }
            }
            $alltuples = array($tuple);
            $res = executeBoundSQL("insert into Patron(patronId, name, homeAddress, partyId) values(patron_id.nextval, :bind1, :bind2, :bind3)", $alltuples);
            if ($success) {
                executeBoundSQL("update Party set partySize=partySize+1 where partyId=:bind3", $alltuples);
                OCICommit($db_conn);
                sendAlert("Patron successfuly created! They can be viewed at the patron dropdown below");
            } else {
                if (str_contains($res, 'unique')) {
                    sendAlert('This name and home address combination for a patron already exists. Please enter unique values.');
                } else {
                    sendAlert('Something went wrong with adding a new patron. Please try again.');
                }
            }
            echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
        }

        // deletes chosen patron from Patron table, and updates party size in Party 
        function deletePatron() {
            global $patronNameAddrs, $db_conn;
            foreach($patronNameAddrs as $tuple) {
                if (str_contains($_POST['patronNameAddrDropdown'], $tuple[0]) && str_contains($_POST['patronNameAddrDropdown'], $tuple[1])) {
                    executePlainSQL("update Party set partySize=partySize-1 where Party.partyId in (select Patron.partyId from Patron where name='".$tuple[0]."' and homeAddress='". $tuple[1]."')");
                    executePlainSQL("delete from Patron where name='".$tuple[0]."' and homeAddress='". $tuple[1]."'");
                    OCICommit($db_conn);
                    unset($patronNameAddrs, $tuple);
                    sendAlert("Deleted the selected patron");
                    echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
                    return;
                }
            }
        }

        // updates values for chosen patron based on user input
        function updatePatron() {
            global $patronNameAddrs, $db_conn, $success;
            foreach($patronNameAddrs as $tuple) {
                if (str_contains($_POST['patronDropdownUpdate'], $tuple[0]) && str_contains($_POST['patronDropdownUpdate'], $tuple[1])) {
                    $chosen = array(
                        ":bind1" => (!empty($_POST['updateName']) ? $_POST['updateName'] : $tuple[0]),
                        ":bind2" =>  (!empty($_POST['updateAddr']) ? $_POST['updateAddr'] : $tuple[1]),
                        ":bind3" => $_POST['partyIdDropdown']
                    );
                    $alltuples = array($chosen);
                    executePlainSQL("update Party set Party.partySize = Party.partySize - 1 where Party.partyid in (select Patron.partyid from Patron where Patron.name='".$tuple[0]."' and Patron.homeAddress='".$tuple[1]."')");
                    OCICommit($db_conn);
                    $res = executeBoundSQL("update Patron set name=:bind1, homeAddress=:bind2, partyId=:bind3 where name='$tuple[0]' and homeAddress='$tuple[1]'", $alltuples);
                    if ($success) {
                        OCICommit($db_conn);
                        executeBoundSQL("update Party p set partySize = partySize + 1 where p.partyid = :bind3", $alltuples);
                        OCICommit($db_conn);
                        $tuple[0] = $_POST['updateName'];
                        $tuple[1] = $_POST['updateAddr'];
                        sendAlert("Patron values successfuly updated! See changes in dropdown");
                    } else {
                        if (str_contains($res, 'unique')) {
                            sendAlert('This name and home address combination for a patron already exists. Please enter unique values.');
                        } else {
                            sendAlert('Something went wrong with adding a new patron. Please try again.');
                        }
                    }
                    echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
                    return;
                }
        }
    }

        // deletes a party and all its associated patrons
        function deleteParty() {
            global $db_conn, $partyIds;
            $curr = strtok($_POST['partyAndPatronsDropdown'], ":");
            for ($i = 0; $i < sizeof($partyIds); $i++) {
                if (str_contains($curr, $partyIds[$i])) {
                    executePlainSQL("delete from Party where partyId=" . $partyIds[$i]);
                    OCICommit($db_conn);
                    sendAlert("Deleted the selected party");
                    echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
                    return;
                }
            }
            
        }
        
        // show sum of player gains across all sessions
        function patronGains() {
                $result = executePlainSQL("SELECT p.patronId as id, p.name as name, SUM(pp.playerGain) as gains
                                            FROM PatronPlays pp, Patron p
                                            WHERE pp.patronId = p.patronId
                                            GROUP BY p.patronId, p.name");

                echo "<table>";
                echo "<tr><th>Patron ID</th><th>Patron Name</th></th><th>Total Gains (in dollars)</th></tr>";

                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td><td>" . $row["GAINS"] ."</td></tr>";
                }
                echo "</table>";
        }

        // redirects to the appropriate POST request
        function handlePOSTRequest() {
            if (connectToDB()) { 
                if (array_key_exists('insertPartyRequest', $_POST)) {
                    createParty();
                } else if (array_key_exists('insertPatronRequest', $_POST)) {
                    createPatron();
                } else if (array_key_exists('deletePatronRequest', $_POST)) {
                    deletePatron();
                } else if (array_key_exists('deletePartyRequest', $_POST)) {
                    deleteParty();
                } else if (array_key_exists('updatePatronRequest', $_POST)) {
                    updatePatron();
                }
        
                disconnectFromDB();
            }
        }

        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('gainsRequest', $_GET)) {
                    patronGains();
                }
            }
        }
        
        // checks whether a certain form has been submitted (I'm pretty sure)
        if (isset($_POST['partySubmit']) || isset($_POST['patronSubmit']) || isset($_POST['patronDelete']) || isset($_POST['partyDelete'])
            || isset($_POST['patronUpdate'])) {
            handlePOSTRequest();
        } else if (isset($_GET['gains'])) {
            handleGETRequest();
        }

        ?>
        <div></div>
        </body>

        <footer>
        <form class="foot" method="POST" action="Home.php">
                <input class="button" type="submit" value="Go Back" name="home">
        </form>
        </footer>
</html>