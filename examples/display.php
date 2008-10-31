<?php
/*
 * This is an example script that will list the contents of the
 * Cloud Files Container (hard-coded below as $CFS_CONTAINER) and present
 * URL's for each storage Object.
 *
 * A visitor clicking on the Object's URL will cause this script to fetch
 * the storage Object and stream it back to the visitor's browser after
 * sending the Object's Content-Type header.
 *
 */
require("cloudfiles.php");

$CFS_USERNAME = "Username";
$CFS_API_KEY = "API Key";
$CFS_CONTAINER = "MyPictures";  # referred to as IMAGES in the example

# deprecated variables
$CFS_ACCOUNT = NULL;
$CFS_AUTH_URL = NULL;

# Authenticate to Cloud Files
#
$auth = new CF_Authentication($CFS_USERNAME, $CFS_API_KEY,
        $CFS_ACCOUNT, $CFS_AUTH_URL);
$auth->authenticate();

# Connect to Cloud Files after authentication
#
$conn = new CF_Connection($auth);

# Grab reference to container
#
$container = $conn->get_container($CFS_CONTAINER);

if ($_GET["display"]) {
    # Display the requested image or throw a 404 if that fails
    #
    try {
        $obj = $container->get_object($_GET["display"]);
        header("Content-Type: " . $obj->content_type);
        $fp = fopen("php://output", "w");
        $obj->stream($fp);
        fclose($fp);
    } catch (Exception $e) {
        header("HTTP/1.1 404 Not Found");
        header("Content-Type: text/html; charset=ISO-8859-1");
        print "<html>\n<body>\n";
        print "<h2>File not found.</h2>";
        print "<a href='/'>Go Back</a>\n";
        print "</body>\n</html>";
    }

} else {
    # List out the stored images creating a link for each one
    # Display the content if the user clicks on a link.
    #
    print "Click an image link to display it in your browser.<br />\n";
    print "<ul>\n";
    $object_list = $container->list_objects();
    foreach ($object_list as $obj) {
        $details = $container->get_object($obj);
        print "<li> ";
        print "<a href='/display.php?display=".$obj."'>".$obj."</a> - ";
        print $details->content_length . " bytes";
        print "</li>\n";
    }
    print "</ul>\n";
}

?>
