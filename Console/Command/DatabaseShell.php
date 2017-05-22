<?php
/* SVN FILE: $Id: schools_controller.php  2013-10-22      02:16:01Z gwoo $ */
/**
 * Object-relational mapper.
 *
 * DBO-backed object data model, for mapping database tables to Cake objects.
 *
 * PHP versions 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @package       cake
 * @version       $Revision:  2013_0.1 $
 * @date				2014-01-26
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Included libs
 */

/**
 *
 * @package   		cake
 * @subpackage 	cake.cake.libs.model
 * @link          		http://book.cakephp.org/view/66/Models
 */

//	var $validate = array();
/**
 * List of validation errors.
 *
 * @var array
 * @access public


This file contains public functions which are run from a command line (or crontab) 
that deal with handling the database in general.

// End of header
*/



class DatabaseShell extends AppShell
{

public function main() {
		$this->out('Hello World.');
}



public function hey_there() {
$this->out('Hey there ' . $this->args[0]);
}




/**
*	This function will generate a complete backup of the database and
*	email it to the administrator
*
*/
public function do_backup() {
// THIS IS A VERY DIRTY HACK. I SHOULD BE ABLE TO RE-USE THE DATABASE CREDENTIALS FROM CAKE-PHP
// THESE 4 VARIABLES SHOULD BE READ FROM A CONFIG FILE. INCLUDING THE ADMIN EMAIL
$this->out('read all the parameters.');
/*
	Configure::load('cronosoft');

	$host = Configure::read('DB_backup.host');
	$user = Configure::read('DB_backup.user');
	$password = Configure::read('DB_backup.password');
	$name = Configure::read('DB_backup.name');


		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '8870mit',
		'database' => 'alc',
		'prefix' => '',




*/

	$host = "localhost";
	$user = "root";
	$pass = "8870mit";
	$name = "alc";

$this->out('parameter read.');
	$filename = $this->backup_tables($host, $user, $pass, $name);
	
/*
	$sendoptions = array('layout' => 'default_email_layout',
							'template'	=> 'reg_status_5',
							'to'	=> 'antoine.de.poorter@gmail.com',
							'subject' => 'Regular backup of database',
							'attachments' => array($filename)
							);
	$rc = $this->send_email($sendoptions, $user_data);

	if ($rc == 0) {
		$this->flash('Backup has been created and sent via email, click to continue '  ,  '/users/admin_panel', 30);
	}
	else {
		if (!empty($filename))    {
			$this->flash('Backup has been created but could NOT be sent via email, click to continue '  ,  '/users/admin_panel', 30);
			}
			else {
				$this->flash('Backup could NOT be created '  ,  '/users/admin_panel', 30);
			}
	}
*/	
	$this->out($filename);		

}


// returns the name of the backup file
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	$return = "";
	
  $link = mysql_connect($host,$user,$pass);
  mysql_select_db($name,$link);

  //get all of the tables
  if($tables == '*')
  {
    $tables = array();
    $result = mysql_query('SHOW TABLES');
    while($row = mysql_fetch_row($result))
    {
      $tables[] = $row[0];
    }
  }
  else
  {
    $tables = is_array($tables) ? $tables : explode(',',$tables);
  }
  //cycle through
  foreach($tables as $table)
  {
    $result = mysql_query('SELECT * FROM '.$table);
    $num_fields = mysql_num_fields($result);

    $return.= 'DROP TABLE '.$table.';';
    $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
    $return.= "\n\n".$row2[1].";\n\n";
    for ($i = 0; $i < $num_fields; $i++)
    {
      while($row = mysql_fetch_row($result))
      {
        $return.= 'INSERT INTO '.$table.' VALUES(';
        for($j=0; $j<$num_fields; $j++)
        {
          $row[$j] = addslashes($row[$j]);
          $row[$j] = ereg_replace("\n","\\n",$row[$j]);
          if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
          if ($j<($num_fields-1)) { $return.= ','; }
        }
        $return.= ");\n";
      }
    }
    $return.="\n\n\n";
  }

//	$destinationFolder = WWW_ROOT.'files/DB_backups'.DS;
	$destinationFolder = '/home/antoine/Dropbox/ALC/dumps'.DS;
	$destinationFile = 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';

  	$handle = fopen($destinationFolder.$destinationFile,'w+');
  	fwrite($handle,$return);
  	fclose($handle);

return ($destinationFolder.$destinationFile);
}






}
