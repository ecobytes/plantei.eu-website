<?php
/*. require_module 'standard'; .*/
require_once(__DIR__.'/../vendor/autoload.php');

$host = 'http://localhost:4444/wd/hub'; // this is the default
$capabilities = DesiredCapabilities::phantomjs();
$connected = false;
$process = new Process('phantomjs --webdriver=4444');
//$process.start();


//remove old screenshots
$files = glob('/vagrant/test-results/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    if($file != '.gitignore'){
      unlink($file); // delete file
    }
}


while(!$connected){
  try{
    $driver = RemoteWebDriver::create($host, $capabilities, 5000);
    $connected = true;
  }catch (Exception $e){
    echo "waiting for webdriver to be available...\n";
    sleep(1);
  }
}
echo "connected.\n\n";
$driver->quit();
$args = $argv;
unset($args[0]);
$argString = ' '. implode(' ', $args);
passthru ('cd /vagrant/src; /vagrant/src/vendor/bin/behat'.$argString);
sleep(1);
if(isset($process)){
  $process->stop();
}

/* An easy way to keep in track of external processes.
* Ever wanted to execute a process in php, but you still wanted to have somewhat controll of the process ? Well.. This is a way of doing it.
* @compability: Linux only. (Windows does not work).
* @author: Peec
*/
class Process{
  private $pid;
  private $command;

  public function __construct($cl=false){
    if ($cl != false){
      $this->command = $cl;
      $this->runCom();
    }
  }
  private function runCom(){
    $command = 'nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
    exec($command ,$op);
    $this->pid = (int)$op[0];
  }

  public function setPid($pid){
    $this->pid = $pid;
  }

  public function getPid(){
    return $this->pid;
  }

  public function status(){
    $command = 'ps -p '.$this->pid;
    exec($command,$op);
    if (!isset($op[1]))return false;
    else return true;
  }

  public function start(){
    if ($this->command != '')$this->runCom();
    else return true;
  }

  public function stop(){
    $command = 'kill '.$this->pid;
    exec($command);
    if ($this->status() == false)return true;
    else return false;
  }
}
?>