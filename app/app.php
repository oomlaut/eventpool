<?php

require_once('lib/RedBean/rb.php');

$db_path = __dir__ . '/../data/eventpool.sqlite';
// echo $db_path;

R::setup('sqlite:' . $db_path);

$app_loaded=true;

class App {

	private $debug = array();

	private $config = null;
	private $dates = null;

	private $data = null;

	/**
	 * store key, value pairs into debug array
	 */
	public function debug($key, $value){
		$this->debug[$key] = $value;
	}

	/**
	 * instantiation magic method
	 */
	public function __construct(){
		$this->config = R::dispense('config');
		$this->dates = R::dispense('dates');
	}

	/**
	 * destruction magie method
	 */
	public function __destroy(){
		R::close();
	}

	/**
	 * magic method called when `echo $instance` is referenced
	 */
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

	/**
	 *
	 */
	public function config($key, $value){
		if(empty($key) || empty($value)):
			die("invalid key/value pairs");
		endif;

		// TODO: abstract db call into helper private method

		$row = R::findOne('config', ' key = ? ', array( $key ));

		if(count($row) === 0):
			$this->config->key = $key;
			$this->config->value = $value;
			R::store($this->config);
		else:
			$item = R::load('config', $row->id);
			$item->value = $value;
			R::store($item);
		endif;

		return $this;
	}

	/**
	 *
	 */
	private function timeFormat($t){
		return $t * 1000;
	}

	/**
	 *
	 */
	private function timeUnformat($t){
		return $t / 1000;
	}

	/**
	 *
	 */
	private function generateRange($date){

		$info = getdate(strtotime($date));

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

		$this->config['offset'] = getdate($starttime)['wday'];

		// $this->debug("time", array("start" => $starttime, "end" => $endtime, "diff", $diff));

		for($i = 0; $i < $diff; $i++){
			$index = $this->timeFormat($starttime);
			$row = R::findOne('dates', ' date = ? ', array($index));
			$claimedBy = (count($row) === 0 ) ? "" : $row->value;
			$this->data->dates[$index] = $claimedBy;
			$starttime += (60*60*24);
		}

		return $this;
	}

	/**
	 *
	 */
	public function fetch(){
		$row = R::findOne('config', ' key = ? ', array( 'date' ));
		$date = $row->value;


		$this->generateRange($date);

		$this->data->offset = $this->config['offset'];
		$this->data->selected = $this->timeFormat(strtotime($date));
		return $this;
	}

	/**
	 *
	 */
	public function toJSON(){
		if(headers_sent()):
			die("unable to display json content-type");
		else:
			header("Content-type:application/json");
			echo json_encode($this->data);
			exit;
		endif;

		return $this;
	}

	/**
	 *
	 */
	public function claim($date, $value){
		$row = R::findOne('dates', ' date = ? ', array( $this->timeUnformat($date) ) );

		if(count($row) === 0):
			$this->dates->date = $date;
			$this->dates->value = $value;
			$store = R::store($this->dates);
		else:
			// do nothing.
		endif;

		return $this;
	}

	/**
	 *
	 */
	private function reset(){
		R::nuke();
		return $this;
	}
}
