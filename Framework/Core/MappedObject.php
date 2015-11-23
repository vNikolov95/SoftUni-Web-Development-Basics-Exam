<?php

namespace Framework\Core;

use \Framework\Core\Database as Database;

class MappedObject
{
	private $db;

	function __construct(){
		$this->db = \Framework\Core\Database::getInstance();
	}

	function Tables()
	{
		static $tables = array();

		if( !count($tables) )
		{
			$result = $this->db->prepare('SHOW TABLES');
			$result->execute();

			while( $row = $result->fetch() )
			{
				$tables[]=array_shift($row);
			}
		}

		return $tables;
	}
	
	function TableExists($strTable)
	{
		return in_array( $strTable, MappedObject::Tables() );
	}

	function MapClassToDbTable($class, $tableName)
	{
		$instance = new $class;

		$classProps = get_object_vars($instance);

		$sql = "create table if not exists ".strtolower($tableName)." (";

			$rels = array();

			foreach($classProps as $pname=>$prop){

				if($pname == "db"){
					continue;
				}

				$c = new \ReflectionProperty($instance, $pname);
				$doc = $c->getDocComment();

				preg_match_all('#@(.*?)\n#s', $doc, $annotations);
				$annotation = $annotations[1][0];

				if(!isset($annotation) || $annotation == false){
					throw new \Exception("Property $pname is not annotated");
				}

				$annotationSplit = preg_split('/\s+/', $annotation);

				$sql = $sql." $pname ";

				foreach($annotationSplit as $anon){
					if(strtolower($anon) === "foreign"){
						$foreignKey = explode("_",$pname);
						$rels[] = "foreign key ($pname) references $foreignKey[0](id)";
					}
					else if(strtolower($anon) === "primary"){
						$rels[] = "primary key ($pname)";
					}
					else{
						$sql = $sql." $anon ";
					}
				}

				$sql = $sql.",";
			}

			foreach($rels as $rel){
				$sql = $sql." $rel,";
			}

		$sql = substr($sql,0,-1)." )";

		$result = $this->db->prepare($sql);

		return $result->execute();
	}

function Class2Table($mxd)
{
	$origClass = is_object($mxd) ? get_class($mxd) : $mxd;

	//var_dump(class_exists(\Framework\Config\Config::MODEL_NAMESPACE.$origClass));exit;

	//$exists = class_exists(\Framework\Config\Config::MODEL_NAMESPACE.$origClass);
	//echo $exists ===false;exit;

	// if(!class_exists(\Framework\Config\Config::MODEL_NAMESPACE.$origClass)){
	// 	trigger_error("Class $origClass does not exist", E_USER_ERROR);
	// }

	$class = array_pop(explode("\\",$origClass));

	if( !MappedObject::TableExists( strtolower($class) ) && $class !='MappedObject' )
	{
		$class = get_parent_class($class);
	}

	$table = array_pop(explode("\\",strtolower($class)));

	if($table=='mappedobject')
	{
		trigger_error("Class $origClass does not have a table representation", E_USER_ERROR);
		return false;
	}

	return $table;
}

function Columns($strTable)
{
	$strTable = MappedObject::class2Table($strTable);

		// cache results locally
	static $cache=array();

		// already cached? return columns array
	if( isset($cache[$strTable]) )
	{
		return $cache[$strTable];
	}
	else
		// connect to database and run 'describe' query to get results
	{
		$result = $this->db->prepare("SHOW COLUMNS FROM $strTable");
		$result->execute(array($strTable));

		if( $result->rowCount() > 0)
		{
			$arrFields = array();
			while( $col = $result->fetch() )
			{
				$arrFields[] = $col['Field'];
			}
				// cache results for future use and return
			return $cache[$strTable] = $arrFields;
		}
		else
		{
			throw new \Exception("Could not decribe table $strTable");
			return false;
		}
	}
}

function Escape($mixVal)
{
		// clean whitespace
	$val = trim( $mixVal );		
		// magic quotes?
	if ( get_magic_quotes_gpc() )
	{
		$val = stripslashes($val);
	}
	return($this->db->quote($val));
}

function GetLinkTable($strClass1, $strClass2)
{
	$array = array( MappedObject::Class2Table($strClass1),MappedObject::Class2Table($strClass2) );
	sort($array);
	return implode( '_', $array);
}

function Link(&$obj1, &$obj2)
{
	$table1=MappedObject::Class2Table($obj1);
	$table2=MappedObject::Class2Table($obj2);
	$linktable = MappedObject::GetLinkTable($table1, $table2);
	$sql = $this->db->prepare("INSERT INTO {$linktable} ({$table1}_id, {$table2}_id) VALUES ({$obj1->id}, {$obj2->id})");
	if( $sql->execute(array($linktable,$table1, $table2, $obj1, $obj2)) )
	{
		return true;
	}
	else
	{
		trigger_error("Failed to link objects: $table1, $table2", E_USER_WARNING);
		return false;
	}
}

function UnLink(&$obj1, &$obj2)
{
	$table1=MappedObject::Class2Table($obj1);
	$table2=MappedObject::Class2Table($obj2);
	$linktable = MappedObject::GetLinkTable($table1, $table2);
	$sql = $this->db->prepare("DELETE FROM {$linktable} WHERE {$table1}_id = {$obj1->id} AND {$table2}_id = {$obj2->id}");
	if( $sql->execute(array($linktable,$table1, $table2, $obj1, $obj2)) )
	{
		return true;
	}
	else
	{
		trigger_error("Failed to unlink objects: $table1, $table2", E_USER_WARNING);
		return false;
	}
}

function &Create($strClass, $arrVals = null)
{
	$className = \Framework\Config\Config::MODEL_NAMESPACE.$strClass;

	$obj = new $className;
	foreach( MappedObject::Columns( $className ) as $key=>$field )
	{
		$obj->$key = $field;
	}
	$obj->populate($arrVals);		
	return $obj;
}

function Count( $strClass, $strWhere='1=1' )
{
	$table = MappedObject::Class2Table($strClass);
	$strSQL = $this->db->prepare("SELECT Count(id) AS count FROM $table WHERE $strWhere");
	$strSQL->execute(array($table,$strWhere));
	if( $arr = $strSQL->fetchAll() )
	{
		return $arr['count'];
	}
	else
	{
		return false;
	}
}

function FindBySql( $strClass, $strSQL, $strIndexBy='id' )
{
	
	static $cache = array();
	$md5 = md5($strSQL);
	
	if( isset( $cache[$md5] ) && defined('MAPPEDOBJECT_CACHE_SQL') && MAPPEDOBJECT_CACHE_SQL )
	{
		return $cache[$md5];
	}
	else
		{	$rscResult = $this->db->prepare($strSQL);

			if( $rscResult->execute() === false)
			{
				
				trigger_error("SQL Query Failed: $strSQL", E_USER_ERROR);
				return $cache[$md5]=false;
			}
			else
			{
				$arrObjects = array();

				while( $arrVals = $rscResult->fetch() )
				{
					$arrObjects[$arrVals[$strIndexBy]] =& MappedObject::Create($strClass, $arrVals );
				}

				return $cache[$md5]=$arrObjects;
			}
		}
	}

	function FindAll( $strClass, $mxdWhere=NULL, $strOrderBy='id ASC', $intLimit=10000, $intOffset=0 )
	{
		$table = MappedObject::Class2Table($strClass);
		$strSQL = "SELECT * FROM $table";
		if($mxdWhere)
		{
			$strWhere = ' WHERE ';
			if( is_array($mxdWhere) )
			{
				$conditions = array();
				foreach($mxdWhere as $key=>$val)
				{
					$val = addslashes($val);
					$conditions[]="$key='$val'";
				}
				$strWhere.= implode(' AND ', $conditions);
			}
			elseif( is_string($mxdWhere) )
			{
				$strWhere.= $mxdWhere;
			}
			$strSQL.=$strWhere;
		}
		// check for single-table-inheritance
		if( strtolower($strClass) != $table )
		{
			$strSQL.= $mxdWhere ? " AND class LIKE '$strClass' ":" WHERE class LIKE '$strClass' ";
		}
		$strSQL.=" ORDER BY $strOrderBy LIMIT $intOffset, $intLimit";

		return MappedObject::FindBySql( $strClass, $strSQL );
	}
	
	function FindFirst( $strClass, $strWhere=NULL, $strOrderBy='id ASC' )
	{
		$arrObjects = MappedObject::FindAll( $strClass, $strWhere, $strOrderBy, 1 );
		if( Count($arrObjects) )
		{
			return array_shift($arrObjects);
		}
		else
		{
			return false;
		}
	}
	
	function FindById( $strClass, $mxdID )
	{
		if( is_array($mxdID) )
		{
			$idlist = implode(', ', $mxdID);
			return MappedObject::FindAll( $strClass, "id IN ($idlist)" );
		}
		else
		{
			$id = intval($mxdID);
			return MappedObject::FindFirst( $strClass, array('id'=>$id) );
		}
	}
	
	function Insert( $strClass, $properties )
	{
		$object = MappedObject::Create($strClass, $properties);
		return $object->save;
	}
	
	function Update( $strClass, $id, $properties )
	{
		$object = MappedObject::FindById($strClass, $id);
		$object->populate(properties);
		return $object->save();
	}
	
	function save()
	{
		$table = MappedObject::Class2Table(get_class($this));

			// check for single-table-inheritance
		if( strtolower(get_class($this)) != $table )
		{
			$this->class = get_class($this);
		}

		$fields = MappedObject::Columns($table);
			// sort out key and value pairs

		foreach ( $this as $key=>$field )
		{
			if($key!='id' && $key!='db' && ctype_alpha(str_replace('_', '', $key)) && $key!='class')
			{
				$val = MappedObject::Escape( isset($this->$key) ? $this->$key : null  );
				$vals[]=$val;
				$keys[]=$key;
				$set[] = "$key = $val";
			}
		}
			// insert or update as required
		if( isset($this->id) )
		{
			$sql="UPDATE $table SET ".implode($set, ", ")." WHERE id={$this->id}";
		}
		else
		{
			$sql="INSERT INTO $table (".implode($keys, ", ").") VALUES (".implode($vals, ", ").")";
		}

		$result = $this->db->prepare($sql);

		$success = (bool)$result->execute();

		if( !isset($this->id) )
		{
			$this->id = $this->db->lastId();
		}

		return $success;
	}
	
	function populate($arrVals)
	{
		if( is_array($arrVals) )
		{
			foreach($arrVals as $key=>$val)
			{
				$this->$key=$val;
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function destroy()
	{
		$table = MappedObject::Class2Table($this);
		$result = $this->db->prepare("DELETE FROM $table WHERE id ={$this->id}");

		return $result->execute(array($table));
	}
	
	function delete()
	{
		return $this->destroy();
	}
	
	function add_child($strClass, $properties=null)
	{
		$object = MappedObject::Create($strClass, $properties);
		$key = MappedObject::Class2Table($this)."_id";
		$object->$key = $this->id;
		return $object;
	}
	
	function attach(&$obj)
	{
		if( $this->id && $obj->id )
		{
			return MappedObject::Link($this, $obj);
		}
		else
		{
			trigger_error('both objects must be saved before you can attach');
			return false;
		}
	}
	
	function detach(&$obj)
	{
		return MappedObject::UnLink($this, $obj);
	}
	
	function find_parent($strClass, $strForeignKey=NULL)
	{
		$key = $strForeignKey or $key=strtolower( $strClass.'_id' );
		return MappedObject::FindById($strClass, $this->$key);
	}
	
	function find_linked($strClass, $mxdCondition=null, $strOrder=null)
	{
		if($this->id)
		{
			// only attempt to find links if this object has an id
			$table = MappedObject::Class2Table($strClass);
			$thistable = MappedObject::Class2Table($this);
			$linktable=MappedObject::GetLinkTable($table, $thistable);
			$strOrder = $strOrder ? $strOrder: "{$strClass}.id";
			$sql= "SELECT {$table}.* FROM {$table} INNER JOIN {$linktable} ON {$table}_id = {$table}.id WHERE $linktable.{$thistable}_id = {$this->id} ORDER BY $strOrder";
			if( is_array($mxdCondition) )
			{
				foreach($mxdCondition as $key=>$val)
				{
					$val = addslashes($val);
					$sql.=" AND $key = '$val' ";
				}
			}
			else
			{
				if($mxdCondition) $sql.=" AND $mxdCondition";
			}
			return MappedObject::FindBySql($strClass, $sql);
		}
		else
		{
			return array();
		}
	}
	
	function h($key)
	{
		return htmlentities($this->$key);
	}
	
	function to_str()
	{
		return get_class($this).' '.$this->id;
	}
}