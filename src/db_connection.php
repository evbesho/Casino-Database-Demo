<?php

// based on code from the CPSC 304 PHP tutorial

$db_conn = NULL;
$success = True;
$show_debug_alert_messages = True;

function connectToDB() {
    global $db_conn;

    // works if the ENTIRE project is put under public_html from the root directory
    // then place creds.json in a folder called cs304 under your home directory
    // file is a regular json file of the form {"username" : "my_username", "password":"my_password"}
    $json = file_get_contents('../../../cs304/creds.json');
    $json_data = json_decode($json, true);    
    
    // connects to the database using credentials
    $db_conn = OCILogon($json_data["username"], $json_data["password"], "dbhost.students.cs.ubc.ca:1522/stu");
    // need to switch CWL w/ your cwl id and 12345678 with your student number (keep the a)
    // $db_conn = OCILogon("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        return true;
    } else {
        $e = OCI_Error();
        echo "connection failed\n";
        echo htmlentities($e['message']) . "<br>";
        echo "Oracle Connect Error " . $e['message'] . "<br>";
        return false;
    }
}

// disconnects from the database
function disconnectFromDB() {
    global $db_conn;

    OCILogoff($db_conn);
}

//takes a plain (no bound variables) SQL command and executes it
function executePlainSQL($query) { 
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $query);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $query . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']) . "<br>";
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        $e = oci_error($statement);
        $success = False;
        return $e['message'];
    } else {
        $success = True;
    }
    return $statement;
}


// opens alert message in popup to user
function sendAlert($message) {
    echo "<script type='text/javascript'>alert('" . $message . "');</script>";
}

function executeBoundSQL($cmdstr, $list) {
 global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {

            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle

            $success = False;
            return $e['message'];
        } else {
            $success = true;
        }
    }
    return $statement;
}
?>
