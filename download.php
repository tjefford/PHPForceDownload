<?php
// Require ForceDownload library
require_once('lib/ForceDownload.php');
// Instantiate ForceDownload object
$download = new ForceDownload;

// Get Paramters from URL
$postid = $_GET['postid'];
$link = $_GET['link'];

/*
 * As a minor security percaution, you might want to
 * limit the sources where files are allowed to come from.
 *
 * For example, zencast, where my podcast is hosted (http://zencast.fm)
 */
$whitelist = array(
  'media.zencast',
  'google'
);

// Is postid set and numeric?
if($postid && is_numeric($postid)) {
  // Is link set and in the approved list?
  if($link && $download->approved($link, $whitelist)){
    // Force the download of the file! (good job)
    $download->build($mp3, $postid, true);
  } else {
    // Link was not approved or non-existent. bummer.
    // Log error in /var/tmp/downloads_error.log
    $download->log_error($postid, $link);
    // Redirect to where they accessed the link
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
} else {
  // post id isnt numeric or non-existent.
  // Log error in /var/tmp/downloads_error.log
  $download->log_error($postid, $link);
  // Redirect to where they accessed the link
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>
No Access
