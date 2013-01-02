<?php

require("config.php");
require_once(WWW_DIR."/lib/postprocess.php");
require("pid.php");

$filename = pathinfo(__FILE__, PATHINFO_FILENAME);

$pidfile = new pidfile("/tmp", $filename);
  if($pidfile->is_already_running()) {
    echo "Already running.\n";
    exit;
  }

$db = new DB();
$query = "select count(rn.releaseID) from releasenfo rn left outer join releases r ON r.ID = rn.releaseID WHERE rn.nfo IS NULL AND rn.attempts < 5";

$i=1;
while($i=1)
{
  $result = $db->query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }

  while ($row = mysql_fetch_assoc($result)) {
    $count = $row['count(rn.releaseID)'];
  }

  if ($count > 10) {
    $postprocess = new PostProcess(true);
    $postprocess->processNfos();
  } else {
    echo "sleeping.....\n";
    sleep(10);
  }
}

mysql_free_result($result);

?>

