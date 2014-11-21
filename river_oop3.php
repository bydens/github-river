<?php 
// On the one bank of the river is a family - father, mother, son and daughter. The family wants to get to the other bank of the river. They found a fisherman with a boat who agreed to borrow a boat to them. Family should get to another bank of the river and return the boat back to a fisherman. Boat can accommodate one adult person or two children.
// Solve this task using php5, utilize OOP, each object should be a class instance. Program should read a configuration from an ini file. It should be possible to change number of adults and/or children to any other number. Program should write a log file with results of its work where anyone could check how people cross the river and how many iterations passed.


class river
{
	public $leftBank = array();
	public $rightBank = array();
	public $arrObjPeople;
	private $txtLog;

	public function __construct($file = false)
	{
		if ($file) {
			$this->readFile($file);
		}
		//travelers are building on the left bank to the right to transport
		$this->leftBank = $this->arrayPeople();
	}

//preparing an array of travelers
	private function arrayPeople()
	{
		if ($this->arrObjPeople) {
			$arrHum_prom = $this->arrObjPeople;
			$fisher = new grown('fisher');//fisher is always there and he should be the first in an array
			$arrHum['grown'][0] = $fisher;
			for ($i = 0; $i < count($arrHum_prom); $i++) { 
				if ($arrHum_prom[$i]->age == 'grown') {
					$arrHum['grown'][] = $arrHum_prom[$i];
				}	elseif ($arrHum_prom[$i]->age == 'child'){
					$arrHum['child'][] = $arrHum_prom[$i];
				}
			}
		}else{
			$arrHum = '';
		}
		return $arrHum;
	}
//read data from ini file
	public function readFile($file)
	{
		if (file_exists($file) && is_readable($file)){	
			$arrIni = parse_ini_file($file, true);
			foreach ($arrIni as $key => $value) {
				if ($key == 'ground') {
					$count = count($value['name']);
					for ($i = 0; $i < $count; $i++) {
						$this->arrObjPeople[] = new grown($value['name'][$i]);
					}
				}
				if ($key == 'child') {
					$count = count($value['name']);
					for ($i = 0; $i < $count; $i++) {
						$this->arrObjPeople[] = new child($value['name'][$i]);
					}
				}
			}
		}
	}
//preparation of data for the recording process of transportation in the log file
	private function printRes($flag = '')
	{
		static $count = 0;
		$result = $count . ' ';
		if ($flag == 'right') {
			$flag = '-------------------------------------------------->';
		} elseif ($flag == 'left'){
			$flag = '<--------------------------------------------------';
		} else{
			$flag = '';
		}
		foreach ($this->leftBank as $age) {
			foreach ($age as $value) {
				$result .= ' ' . $value->name;
				if ($value->age) {
					$result .= '(' . $value->age . ')';
				}
			}
		}
		if ($this->rightBank) {
			$result .= $flag;
			foreach ($this->rightBank as $age) {
				foreach ($age as $value) {
					$result .= ' ' . $value->name;
					if ($value->age) {
						$result .= '(' . $value->age . ')';
					}
				}
			}		
		}
		$result .= "\n";
		$count++;
		return $result;
	}
//crossing
	public function crossing()
	{
		if ($this->leftBank) {
			$k = count($this->leftBank['grown']) - 1;
			$c = count($this->leftBank['child']) - 1;
			$this->txtLog = $this->printRes();
			// first crossing ------------------------------------------
			for ($j = $c; $j > $c - 2; $j--) { 
			 	$this->rightBank['child'][$j] = $this->leftBank['child'][$j];
				$this->leftBank['child'][$j] = '';
			}
			$flag = 'right';
			$this->txtLog .= $this->printRes($flag);
			//crossing of children --------------------------------------------
			while ($this->leftBank['child'][0]) {
				$this->leftBank['child'][$c - 1] = $this->rightBank['child'][$c - 1];
				$this->rightBank['child'][$c - 1] = '';
				$flag = 'left';
				$this->txtLog .= $this->printRes($flag);
				for ($t = $c - 1; $t >= $c - 2 ; $t--) { 
					$this->rightBank['child'][$t] = $this->leftBank['child'][$t];
					$this->leftBank['child'][$t] = '';
				}
				$flag = 'right';
				$this->txtLog .= $this->printRes($flag);
				$c--;
			}
			//crossing of grown
			while ($this->leftBank['grown'][$k]) {
				//вторая переправа --------------------------------------------
				$this->leftBank['child'][0] = $this->rightBank['child'][0];
				$this->rightBank['child'][0] = '';
				$flag = 'left';
				$this->txtLog .= $this->printRes($flag);
				// --------------------------------------------
				$this->rightBank['grown'][$k] = $this->leftBank['grown'][$k];
				$this->leftBank['grown'][$k] = '';
				$flag = 'right';
				$this->txtLog .= $this->printRes($flag);
				//-----------------------------------------
				$this->leftBank['child'][1] = $this->rightBank['child'][1];
				$this->rightBank['child'][1] = '';
				$flag = 'left';
				$this->txtLog .= $this->printRes($flag);
				// ---------------------------------------------
				for ($i = 0; $i < 2; $i++) { 
					$this->rightBank['child'][$i] = $this->leftBank['child'][$i];
					$this->leftBank['child'][$i] = '';
				}
				$flag = 'right';
				$this->txtLog .= $this->printRes($flag);
				$k--;
			}
			//com back fisher
			$this->leftBank['grown'][0] = $this->rightBank['grown'][0];
			$this->rightBank['grown'][0] = '';
			$flag = 'left';
			$this->txtLog .= $this->printRes($flag);
		}else{
			$this->txtLog = 'Error read .ini file.';
		}
	}
	//Write the process of crossing in log file
	public function writeLog(){
		file_put_contents('crossing.log', $this->txtLog);
	}
	//Conclusion crossing into the browser
	public function writeInBrowser(){
		echo "<h1>Crossing.</h1>" . nl2br($this->txtLog);
	}
}
//class of people for the crossing
class human
{
	public $name;
	public function __construct($value)
	{
		$this->name = $value;
	}
}
class grown extends human
{
	public $age = 'grown';
}

class child extends human
{
	public $age = 'child';
}

//----------------------------------------------------------------
$runing = new river('people.ini');
$runing->crossing();//
$runing->writeLog();
$runing->writeInBrowser();



 ?>