<html lang="EN">

<head>
    <title>Manage Casino Rooms</title>
</head>

<body>
<h1>Manage Rooms</h1>

<form method="POST" action="room.php">
    <h2>Create New Room</h2>
    <input type="hidden" id="insertRoomRequest" name="insertRoomRequest">
    Room Number: <input class="input_field" type="number" name="roomNum" required>
    <div></div>
    Cost Per Night: <input class="input_field" type="number" name="nightlyCost" required>
    <div></div>
    <label for="getBranchNames">Available Branches (you must choose one):</label>
    <select class="input_field" name="getBranchNames" id="getBranchNames">
        <?php
        require './db_connection.php';

        // creates a dropdown with the existing branchNames
        function branchDropdown() {
            if (connectToDB()) {
                $names = executePlainSQL("select name from CasinoBranch");
                while($row = OCI_Fetch_Array($names, OCI_BOTH)) {
                    echo "<option value='".$row['NAME']."'>". $row['NAME'] . "</option>";
                }
                disconnectFromDB();
            }
        }

        branchDropdown();
        ?>
    </select>
    <div></div>
    <input class="button" type="submit" value="Add" name="roomSubmit">
</form>

<form method="POST" action="room.php">
    <h2>Assign Room to Party</h2>
    <input type="hidden" id="assignRoom" name="assignRoom">
    <div></div>
    <label for="getRooms">Rooms:</label>
    <select class="input_field" name="getRooms" id="getRooms">
        <?php

        // creates a dropdown with the existing rooms

        function roomDropdown() {
            if (connectToDB()) {
                $names = executePlainSQL("select * from Room");
                while ($row = OCI_Fetch_Array($names, OCI_BOTH)) {
                    echo "<option value='".$row['ROOMNUM']."'>" . "Branch Name: " . $row['BRANCHNAME'] . "; Room Number: " . $row['ROOMNUM'] . "; Nightly Cost: " . $row['NIGHTLYCOST'] . "</option>";
                }
                disconnectFromDB();
            }
        }

        roomDropdown();
        ?>
    </select>
    <div></div>

    <input type="hidden" id="assignRoomParty" name="assignRoomParty">
    <label for="getParty">Parties</label>
    <select class="input_field" name="getParty" id="getParty">
        <?php

        // creates a dropdown with the existing party info
        function getParties() {
            if (connectToDB()) {
                $result = executePlainSQL("select * from Party");
                while($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo "<option value='".$row['PARTYID']."'>" . 'Party ID: ' . $row['PARTYID']. '; Room No: ' .$row['ROOMNUM'] . '; Account No: ' .$row['ACCOUNTNUM'].
                        '; Balance: $ '.$row['BALANCE']. '; Size: ' .$row['PARTYSIZE'] . '; Stay Start Date: ' .
                        $row['STARTDATE'].'; Stay End Date: '. $row['ENDDATE'] . "</option>";
                }
                disconnectFromDB();
            }
        }

        getParties();
        ?>
    </select>
    <div></div>
    <input class="button" type="submit" value="Assign" name="roomAssign">
</form>

<form method="POST" action="room.php">
    <h2>Delete a Room</h2>
    <input type="hidden" id="deleteRoomRequest" name="deleteRoomRequest">
    <div></div>
    <label for="delRoom">All Active Rooms: </label>
    <select class="input_field" name="delRoom" id="delRoom">
        <?php roomDropdown(); ?>
    </select>
    <div></div>
    <input class="button" type="submit" value="Confirm" name="roomDelete">
</form>


<?php

// inserts a new room with given info into the db
function createRoom() {
    global $db_conn, $success;

    $tuple = array(
        ":bind1" => $_POST['roomNum'],
        ":bind2" => $_POST['nightlyCost'],
        ":bind3" => $_POST['getBranchNames']
    );

    $alltuples = array($tuple);
    $res = executeBoundSQL("insert into Room(roomNum, nightlyCost, branchName) values(:bind1, :bind2, :bind3)", $alltuples);
    if ($success) {
        OCICommit($db_conn);
        sendAlert("Room successfuly created! It can be viewed in the room dropdown below");
    } else {
        if (str_contains($res, 'unique')) {
            sendAlert('This room number already exists. Please enter a unique value.');
        } else {
            sendAlert('Something went wrong with adding a new room. Please try again.');
        }
    }
    echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
}

// assigns room to party
function assignRoom() {
    global $db_conn, $success;
    $tuple = array(
        ":bind1" => $_POST['getRooms'],
        ":bind2" => $_POST['getParty']);
    $alltuples = array($tuple);
    $branchName = executeBoundSQL("select branchName from Room where roomNum=:bind1");
    executeBoundSQL("update Party set roomNum = :bind1 where partyID=:bind2", $alltuples);
    executeBoundSQL("insert into CasinoServes(partyID, branchName) values(:bind2, $branchName)", $alltuples);
    if ($success) {
        OCICommit($db_conn);
        sendAlert("Party room successfuly updated!");
    } else {
        sendAlert('Something went wrong with assigning the room. Please try again.');
    }
    echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
}

// deletes chosen room from database and its number from all parties associated with it
function deleteRoom() {
    global $db_conn, $success;
    $tuple = array(":bind1" => $_POST['delRoom']);
    $alltuples = array($tuple);

    executeBoundSQL("delete from Room where roomNum=:bind1", $alltuples);
    OCICommit($db_conn);

    if ($success) sendAlert("Room successfuly deleted!");
    else sendAlert('Something went wrong with deleting the room. Please try again.');

    echo "<script type='text/javascript'>location.replace(location.href.split('#')[0]);</script>";
}



// redirects to the appropriate POST request
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('insertRoomRequest', $_POST)) {
            createRoom();
        } else if (array_key_exists('assignRoom', $_POST)) {
            assignRoom();
        } else if (array_key_exists('deleteRoomRequest', $_POST)) {
            deleteRoom();
        }

        disconnectFromDB();
    }
}

// checks whether a certain form has been submitted
if (isset($_POST['assignRoom']) || isset($_POST['insertRoomRequest']) || isset($_POST['deleteRoomRequest'])) {
    handlePOSTRequest();
} else if (0 == 1) {
    handleGETRequest();
}

?>
<br>

</body>

<footer>
    <form class="foot" method="POST" action="Home.php">
        <input class="button" type="submit" value="Go Back" name="home">
    </form>
</footer>
</html>