<?php
/**
 * Force download files from whitelisted sources.
 * track file downloads and log errors.
 *
 * @author     Tyler Jefford <http://tylerjefford.com>
 * @version    Release: 1.1
 */


// DotENV
require_once(__DIR__ . '/../vendor/autoload.php');
(new \Dotenv\Dotenv(__DIR__.'/../'))->load();

class ForceDownload {

  /**
   * Normalize filename based on URL given
   * @var string file source feom URL Params
   * @return string
   */
  function filename($file)
  {
    return basename($file);
  }

  /**
   * Auto detect mime-type of file
   * @var string file source feom URL Params
   * @return string
   */
  function mime_type($file)
  {
    $applicable_types = array(
      "zip" => "application/zip",
      "jpg" => "image/jpg",
      "gif" => "image/gif",
      "pdf" => "application/pdf",
      "png" => "image/png",
      "jpeg"=> "image/jpg",
      "mp3" => "audio/mp3"
    );

    $file_extension = strtolower(substr(strrchr($file,"."),1));

    if(array_key_exists($file_extension, $applicable_types)){
      $mime_type = $applicable_types[$file_extension];
    } else {
      $mime_type = "application/force-download";
    };

    return $mime_type;
  }

  /**
   * Build download object with php headers, log analytics in DB
   * @var string file source feom URL Params
   * @var string Post ID from URL Params
   * @return file download
   */
  function build($file, $post, $save = true)
  {
    @ob_end_clean();
    if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');
    header('Content-Type: ' . $this->mime_type($file));
    header('Content-Disposition: attachment; filename="'.$this->filename($file).'"');
    header("Content-Transfer-Encoding: binary");
    header('Accept-Ranges: bytes');

    if($save){
      $this->log($post);
    }
  }

  /**
   * Retrieve requesters IP address for analytics
   * @return string
   */
  function get_ip()
  {
    return $_SERVER['REMOTE_ADDR'];
  }

  /**
   * DB Connect through PDO.
   * @return PDO object
   */
  private function connect()
  {
    return new PDO('mysql:host='.getenv('DBHOST').';dbname='.getenv('DBNAME').';charset=utf8mb4', getenv('DBUSER'), getenv('DBPASS'));
  }

  /**
   * Log analytics data into database
   * @var string Post ID from URL Params
   */
  function log($post="")
  {
    $db = $this->connect();
    $i = $db->prepare("INSERT INTO downloads (ip, post_id, created_at) VALUES (:ip, :post, NOW())");
    $i->execute(array(
      "ip"   => $this->get_ip(),
      "post" => $post
    ));

    $db = null; // close connection
  }

  /**
   * Check if source is approved for download.
   * @var string file source feom URL Params
   * @var array Array of accepted sources
   * @return bool
   */
  function approved($source, $array)
  {
    foreach($array as $item) {
      if (stripos($source,$item) !== false) return true;
    }

    return false;
  }

  /**
   * Log errors into a log file.
   * @var string Post ID from URL Params
   * @var string Download Link from URL Params
   */
  function log_error($post, $link){
    $ip = $this->get_ip();
    $msg = "WARNING: $ip tried to download $link on post $post and failed.\n";
    $log = "/var/tmp/downloads_error.log";

    error_log($msg, 3, $log);
  }

}
