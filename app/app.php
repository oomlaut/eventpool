<?php

require_once('lib/RedBean/rb.php');

$db_path = __dir__ . '/../data/eventpool.sqlite';
// echo $db_path;

R::setup('sqlite:' . $db_path);

$app_loaded=true;

class App {

	private $debug = array();

	private $config = null;

	private $data = null;


	public function __construct(){
		$this->debug["initialized"] = true;
		$this->data = json_decode('{"dates": []}');

		$this->config = R::dispense('config');
	}


	public function __destroy(){
		R::close();
	}

	public function setup($key, $value){
		if(empty($key) || empty($value)):
			die("invalid key/value pairs");
		endif;

		$this->config->key = $key;
		$this->config->value = $value;

		$this->debug("config", $this->config);

		return R::store($this->config);
	}

	public function debug($key, $value){
		$this->debug[$key] = $value;
	}

	public function __toString(){
		ob_start();
		if(!headers_sent()):
			header('Content-type: text/plain');
			print_r($this->debug);
		else :
			echo '<pre>';
			print_r($this->debug);
			echo '</pre>';
		endif;

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	private function timeFormat($t){
		return $t * 1000;
	}

	private function generateRange($date){

		$time = strtotime($date);
		$info = getdate($time);

		// $this->debug('info', $info);

		$prev = $info;
		if($info['mon'] === 1):
			$prev['mon'] = 12;
			$prev['year']--;
		else:
			$prev['mon']--;
		endif;

		$next = $info;
		if($info['mon'] === 12):
			$next['mon'] = 1;
			$next['year']++;
		else:
			$next['mon']++;
		endif;


		$starttime = strtotime($prev['year'] . '-' . $prev['mon'] . '-' . $prev['mday']);
		$endtime = strtotime($next['year'] . '-' . $next['mon'] . '-' . $next['mday']);

		$diff = floor(($endtime - $starttime) / (60*60*24));


		$this->debug("time", array("start" => $starttime, "end" => $endtime, "diff", $diff));

		for($i = 0; $i < $diff; $i++){
			$this->data->dates[$this->timeFormat($starttime)] = "";
			$starttime += (60*60*24);
		}

		$this->debug('data', $this->data);

		return $this;
	}

	public function fetch(){
		$row = R::findOne('config', ' key = ? ', array( 'date' ));
		$date = $row->value;
		$this->generateRange($date);
		$this->data->selected = $this->timeFormat(strtotime($date));
		return $this;
	}

	public function toJSON(){
		if(headers_sent()):
			die("unable to display json format");
		else:
			header("Content-type:application/json");
			echo json_encode($this->data);
			exit;
		endif;

		return $this;
	}
}
