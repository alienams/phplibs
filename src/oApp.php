<?php 
namespace libams;

use \PDO; //wajib jika menggunakan PDO connection

ini_set("memory_limit","1280M");
class oApp {


	public function __construct($idapp='',$host='127.0.0.1',$udb='root',$pwddb='',$db='',$port='3306')
	{
		$this->link = 0;
		$this->cSql = '';
		$this->host=$host;
		$this->port=$port;
		$this->userdb=$udb;
		$this->db=$db;
		$this->passdb=$pwddb;
		$this->idapp= isset($idapp) ? $idapp : '';
		$this->dsn='';
		$this->oData= [];

		set_exception_handler(function($e) {
    	http_response_code(200);
    	header('Content-Type: application/json');
    	echo json_encode(["status" => 0, "msg" => "Fatal error: " . $e->getMessage()]);
    	exit;
		});

		if ( ! file_exists('conf'))
		{
			mkdir("conf",0775,true);	
		}

		if ( ! file_exists(dirname(__dir__,1)."/conf/config.inc.conf") )
		{
			$fo = fopen(dirname(__dir__,1)."/conf/config.inc.conf","w");
			fclose($fo);
			file_put_contents(dirname(__dir__,1)."/conf/config.inc.conf","host=localhost".PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents(dirname(__dir__,1)."/conf/config.inc.conf","db=".PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents(dirname(__dir__,1)."/conf/config.inc.conf","port=3306".PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents(dirname(__dir__,1)."/conf/config.inc.conf","userdb=".PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents(dirname(__dir__,1)."/conf/config.inc.conf","passdb=".PHP_EOL , FILE_APPEND | LOCK_EX);
		}
	}
	
	public  function test()
	{
		return "Ready " ;
	}

	public  function connect()
	{
		try 
		{
			$this->dsn="mysql:dbname=".$this->db.";host=".$this->host.";charset=utf8";
			
			$this->link = new PDO($this->dsn,$this->userdb,$this->passdb,
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,     // Aktifkan error mode exception
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Ambil data sebagai array asosiatif
			]);

		return true;

		}catch (PDOException $e) 
		{ 
			 return false;
		}
	}

	public function to_sql($nametable,$mode_operation,$array,$txtcriteria)
	{
		if ( $this->connect() ) 
		{
			$sql = "show columns from ".$nametable;
			$rq = $this->link->query($sql);
			$rcv = $array;
			$arraykeys = array_keys($array);
			$nkolom = count($rcv);

			switch($mode_operation)
			{
				case "ADD" :
				$cmm = "insert into ".$nametable."(";
				$val = "(";
				//while ($pointer_kolom = $rq->fetch_object()) //mysqli_fetch_object($rq)
				while ($pointer_kolom = $rq->fetch(PDO::FETCH_OBJ)) //mysqli_fetch_object($rq)
				{
					for ($look = 0; $look < $nkolom ;$look++)
					{
						if ( strtolower(trim($pointer_kolom->Field)) == strtolower(trim($arraykeys[$look])) )
						{
							$cmm .= $pointer_kolom->Field.",";
							$val .=  "'".$rcv[$arraykeys[$look]]."',";
						} 
					} 

				}
				$cmm = substr($cmm,0,strlen($cmm)-1).")values";
				$val = substr($val,0,strlen($val)-1).")";
				$cmm .= $val ;
				break ;

				

				case "UPDATE":
				$cmm = "update ".$nametable." set ";
				while ($pointer_kolom = $rq->fetch(PDO::FETCH_OBJ)) //mysqli_fetch_object($rq)
				{
					for ($look = 0; $look < $nkolom ;$look++)
					{
						if ( strtolower(trim($pointer_kolom->Field)) == strtolower(trim($arraykeys[$look])) )
						{
							$cmm .=  $pointer_kolom->Field."="."'".$rcv[$arraykeys[$look]]."',";
						} 
					}
				}
				$cmm = substr($cmm,0,strlen($cmm)-1)." where ".$txtcriteria;
				break;

				
				case "DELETE":
				$cmm = "delete from  ".$nametable." where ".$txtcriteria;
				break;
				
			
				case "OPT":
				$cmm = "";
				while ($pointer_kolom = $rq->fetch(PDO::FETCH_OBJ)) //mysqli_fetch_object($rq)
				{
					for ($look = 0; $look < $nkolom ;$look++)
					{
						if ( $pointer_kolom->Field == $arraykeys[$look])
						{
						$cmm .=  $pointer_kolom->Field."="."'".$rcv[$arraykeys[$look]]."',";
						}
					}
				}
				$cmm = substr($cmm,0,strlen($cmm)-1);
				break;

				default :
				$cmm = "select 'error sql command' as pesan";
				break;
			}
			return $cmm; #Result query
		}else
		{
			return "Koneksi ke server gagal";
		}

	} #end to_sql 


	public  function ptable_to_array($sql)
	{
		if ( $this->connect() )
		{
			$response = array();
			if ( $rest = $this->link->query($sql) )
			{
				while ( $brs =  $rest->fetch(PDO::FETCH_ASSOC) )
				{
					array_push($response,$brs);
				}
			} 
			return 	 $response;
		}else
		{
			return "Gagale konek ke servers";
		}
	} #end of myobj_table_to_array


	public function ptable_to_json($sql)
	{
	if ( $this->connect() )
	{
		$response = array();
		if ( $rest = $this->link->query($sql) )
		{
			while ( $brs =  $rest->fetch(PDO::FETCH_ASSOC) )
			{
				array_push($response,$brs);
			}
		} 
		return 	 json_encode($response);
	}else
	{
		return "Gagale konek ke servers";
	}


	} #End of myobj_table_to_json

	public function ptable_to_object($sql)
	{
		if ( $this->connect() )
		{
			$response=[];
			$nloop = 0;
			if ( $rest = $this->link->query($sql) )
			{
				while ( $brs =  $rest->fetch(PDO::FETCH_OBJ) )
				{
					$response[$nloop] =  $brs;	 
					$nloop ++;
				}
			} 
			return 	 $response;
		}else
		{
			return "Gagale konek ke servers";
		}
	} #end of myobj_table_to_object

	public  function execute_sql($cSql)
	{
		$sresult = [];
		try 
		{
			$this->connect();
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $this->link->prepare($cSql);
			$stmt->execute();
			$sresult=array("status"=>1,"msg"=>"Success","sql"=>$cSql);
		}
		catch (PDOException $e)
		{
			$sresult=array("status"=>0);
		}
		catch (Exception $e)
		{
			$sresult=array("status"=>0);
		}
		return  $sresult;
	} #end of execute_sql


	public function ptable_to_xml($oData)
	{
	  $nomor=1;
	  $xml = new SimpleXMLElement('<table/>');
	  forEach($oData as $key=>$val)
		{
			$row = $xml->addChild("row");  
			$keys = array_keys((array)$val);
			forEach($keys as $x=>$y)
			{
				$xk=$keys[$x];
				//Mencegah adanya karakter khusu yang membuat error generate XML
				$xl=htmlspecialchars($val->$xk, ENT_XML1 | ENT_QUOTES, 'UTF-8'); 
				$row->addChild("$xk",$xl ); 
				$row->addChild("rowid", $nomor); 
			}
			 $nomor ++;
		}
		return $xml->asXML();
	}
	
	public function xml_to_array($oXml)
	{
		$xml = simplexml_load_string($oXml);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		return $array ;
		
	}
	
	public function xml_to2_array($oXml)
	{
		$xml = new SimpleXMLElement($oXml,LIBXML_NOCDATA);
		return (array)$xml;
		
	}
	
	public function remove_special_char($cVars)
    {
		$creturn = trim(str_replace(array('&'),' dan ',$cVars));
    	return trim(str_replace(array('/','#','$','&','@','!','/','<','>','%'),' ',$creturn));
    }
	
	public function date_to_mysqlformat($xdate)
	{
		return date("Y-m-d",strtotime($xdate));
	}

} #ENd of Classs
