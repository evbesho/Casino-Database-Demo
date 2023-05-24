<html>
    <head>
        <Title>View Information About Your Chosen Table</Title>

        <style>
            <?php
            $css = file_get_contents('styles.css');
            echo $css;
            ?>
        </style>

    </head>

    <body>

    <form method="POST" action="Projection.php">
        <h2>Choose Which Information to Display</h2>
            <input class="input_field" type="hidden" id="projectionRequest" name="projectionRequest">
            <?php echo "<input class=input_field type=hidden id=tablesDropdown name=tablesDropdown value=".$_POST['tablesDropdown'].">"; ?>
            <div></div>
        <?php 
            require './db_connection.php';

            // map w/ key of table name in DB, and value being an array of tuples that has all attributes of the relation, 
            // where each tuple contains the name of the attribute in the DB and the label the user sees
            $allAttribues = array(
                "CasinoBranch" => array(array("name","Name"), array("balance","Balance")),
                "Room" => array(array("roomNum","Room Number"), array("nightlyCost","Nightly Cost"), 
                            array("branchName","Branch Name")),
                "CasinoTable" => array(array("tableNum","Table Number"), array("capacity","Capacity"), 
                                    array("branchName","Branch Name")),
                "Game" => array(array("name","Name"), array("variant","Varaiant"), array("numPlayers","Number of Players")),
                "Dealer" => array(array("dealerId","Dealer ID"), array("name","name"), array("homeAddress","Home Address"), 
                                array("wage","Wage")),
                "Party" => array(array("partyId","Party ID"), array("accountNum","Account Number"), array("balance","Balance"), 
                            array("roomNum","Room Number"), array("partySize","Party Size"), array("startDate","Stay Start Date"), 
                            array("endDate","Stay End Date")),
                "Patron" => array(array("patronId","Patron ID"), array("name","name"), array("homeAddress","Home Address"), 
                                array("partyId","Party ID")), 
                "GameSession" => array(array("sessionId","Session ID"), array("TO_CHAR(startTime, 'DD-MON-YYYY HH24:MI:SS') as starttime","Start Time"), 
                                    array("TO_CHAR(endTime, 'DD-MON-YYYY HH24:MI:SS') as endtime","End Time"), array("gameName","Game Name"), 
                                    array("gameVariant","Game Variant"), array("dealerId","Dealer ID"), 
                                    array("branchName","Branch Name"), array("tableNum","Table Number"), 
                                    array("casinoGain","Casino Gain")), 
                "CasinoServes" => array(array("partyId","Party ID"), array("branchName","Branch Name")), 
                "PatronPlays" => array(array("sessionId","Session ID"), array("patronId","Patron ID"), 
                                    array("playerGain","Patron Gain"))
            );

            $chosenTable = $_POST['tablesDropdown'];

            // creates checkboxes for all attributes of the chosen table
            function checkBoxes() {
                global $allAttribues, $chosenTable, $chosenCols;
                foreach ($allAttribues[$chosenTable] as $column) {
                    echo '<input class="input_field" type="checkbox" id="'.$column[0].'"name="'.$column[0].'" value="'.$column[0].'">';
                    echo '<label for="'.$column[1].'">'.$column[1].'</label><br>';
                }
                
            }
            checkBoxes();

        
        ?>
        <input class="button" type="submit" value="View" name="projectCheckbox">

    </form>
    <div></div>
    <form class="foot" method="POST" action="Selects.php">
<input class="button" type="submit" value="Go Back" name="back">
    <?php
    
            // shows the checked attributes of all tuples from chosenTable 
            function showSelect() {
                global $chosenTable, $allAttribues;
                $columns = "";
                $colNames = array();
                foreach($allAttribues[$chosenTable] as $column) {
                    if (array_key_exists($column[0], $_POST) 
                    || (array_key_exists("TO_CHAR(startTime,_'DD-MON-YYYY_HH24:MI:SS')_as_starttime", $_POST) && strcmp($column[0], "TO_CHAR(startTime, 'DD-MON-YYYY HH24:MI:SS') as starttime") == 0)
                    || (array_key_exists("TO_CHAR(endTime,_'DD-MON-YYYY_HH24:MI:SS')_as_endtime", $_POST) && strcmp($column[0], "TO_CHAR(endTime, 'DD-MON-YYYY HH24:MI:SS') as endtime") == 0)) {
                        $columns = $columns . $column[0] . ", ";
                        array_push($colNames, $column[1]);
                     }
                }
                $columns = substr($columns, 0, -2);
                $result = executePlainSQL("SELECT ". $columns . " FROM " . $chosenTable);

                echo "<table>";
                echo "<tr>";
                foreach($colNames as $name) {
                    echo "<th>" . $name . "</th>";
                }
                echo "</tr>";
                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                    echo '<tr>';
                    for($i = 0; $i < sizeof($colNames); $i++) {
                        echo "<td>" . $row[$i] . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
                
            }

            function handlePOSTRequest() {
                if (connectToDB()) {
                    if (array_key_exists("projectionRequest", $_POST)) {
                        showSelect();
                    }
                    disconnectFromDB();
                }
            }

            if (isset($_POST['projectCheckbox'])) {
                handlePOSTRequest();
            }

    ?>

        </form>

    </body>


</html>