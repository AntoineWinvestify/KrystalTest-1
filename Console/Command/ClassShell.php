<?php
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

/*
This file contains public functions which are run from a command line (or crontab) 
that deal with managing the classes in general

// End of header
*/



class ClassShell extends AppShell
{

public function main() {
		$this->out('Hello World.');
}






/*
CALl IT FROM THE CMD LINE AS FOLLOWS:

antoine@rohan:/var/www/app/vendors/shells$ ./../cakeshell  backup -cli /usr/bin/php -console /var/www/cake/console -app /var/www/app    


*/

public function testing() {
	echo "this is the test script running \n";
	$serial_result = "Antoine de Poorter";
//	$fhandle = fopen("/home/antoine/testname.txt", "a+");
 	$fhandle = fopen("testname.txt", "a+");
	fwrite($fhandle, $serial_result);
	fclose($fhandle);
	
}







/**
* Cycles through ALL the groups with the flag activeSchoolYear = 1 and will create
* a new classlog record and its associated attendance records. The latter ones are also linked to
* the corresponding "student" object.

THIS ACTION SHOULD ALSO CREATE THE ATTENDANCE RECORDS, WITH SOME DEFAULT DATA.
the validates field should change to status (with values"defined", "validated",..)
cronjob will create them with state "defined". The teacher will change it to ready" and
the supervisor changes it to "validated"
*/
function cronjob() {
	$this->autoRender = TRUE;		// Later change to FALSE

	$today = getdate();


// FOR TESTING ONLY
	$today[wday] = 3;
	$today[mon]  = 1;
	$today[mday] = 3;
	$today[hours] = 17;
	$today[minutes] = 3;
	$today[year] = 2014;	
	$this->print_r2($today);	
// FOR TESTING ONLY
	
	
	$currentWeekday = $today[wday];			// 1 = Monday, 2 = Tuesday etc....
	$currentTime = $today[hours] * 100 + $today[minutes];

	$activeSchoolYear = 1;
	$groupList = $this->Group->findAllByActiveschoolyear($activeSchoolYear, $fields = array('id', 'teacher_id'), $order = '',
																		$limit = '', $page = '', $recursive = -1);

	$groupList = Hash::extract($groupList, '{n}.Group.id');

$this->print_r2($groupList);

// construct string of date
	if ($today[mon] < 10) {		
			$today[mon] = str_pad($today[mon], 2, "0", STR_PAD_LEFT);	
	}
	if ($today[mday] < 10) {		
			$today[mday] = str_pad($today[mday], 2, "0", STR_PAD_LEFT);
	}
		
	$currentDate = $today[year] . $today[mon] . $today[mday];


	foreach ($groupList as $value) {   //$key = id of group
echo "__________________________<br>";	
		$groupData = $this->Group->findAllById($value, $fields = '', $order = '',
																	$limit = '', $page = '', $recursive = -1);    
	
		if ($this->checkHoliday($currentDate, $school_id) == 0) {		// continue of NO holiday
			$courseData = $this->Coursetimetable->findAllByGroup_id($value, $fields = '', $order = '',
																		$limit = '', $page = '', $recursive = -1);  
				foreach ($courseData as $key1 =>$value1)  {
					if ($currentWeekday == $value1[Coursetimetable][dayofweek]) {
echo "CURRENT WEEKDAY OK<br>";
						if ($currentTime > $value1[Coursetimetable][starttime]) {
						$presentcourseDateTime = $currentDate . $value1[Coursetimetable][starttime];
						$lastcourseDateTime = $groupData[0][Group][lastclasslogrecord];

echo "lastcourseDatetime = $lastcourseDateTime and presentcourseDateTime = $presentcourseDateTime<br>";
				
							if ($lastcourseDateTime != $presentcourseDateTime) {  
							// only create a new record for a new class, do not duplicate
							// probably ok with checking the starttime only, not the date
							
				// create classlog record				
		echo "CREATING NEW RECORD<br>";
							$this->Classlog->create();
							$newdata = Array
											(
								Classlog => Array
									(
									group_id => $value,
									status => FUTURE,
									incident => "", 
									contents => "",
									activeschoolyear => ACTIVESCHOOLYEAR,
									starttime => $value1[Coursetimetable][starttime],
									stoptime => $value1[Coursetimetable][stoptime],
									month => $today[mon],
									year => $today[year],				
									day => $today[mday],
									)
									);

							if ($this->Classlog->save($newdata)) {
								$classlog_id = $this->Classlog->id;
					
// update group record with information of last saved classlog record
								$lastClasslogRecord = $currentDate . $value1[Coursetimetable][starttime];
								$data = array(id => $value, lastclasslogrecord => $lastClasslogRecord);
								$this->Group->save($data);
				
// PREPARE ALL RELEVANT Attendance records
// ONLY TAKE THE STUDENTS RECORDS WHICH HAVE STATUS = "ACTIVE"
								$studentList = $this->Student->findAllByGroupId($value, $fields = '',  $order = '',
																		$limit = '', $page = '', $recursive = -1);
								
								foreach ($studentList as $value2)	{

									$this->Attendance->create(); 		
									$student_id = $value2[Student][id];			
									$this->newdata_att = array (Attendance => Array
														(
														year	=> $today[year],
														month => $today[mon] ,
														day 	=> $today[mday], 
														attendance 		=> 0,
														student_id 		=> $student_id,
														classlog_id 	=> $classlog_id,
														status => 0,			
														)
														);
									$this->print_r2($this->newdata_att);	
									if ($this->Attendance->save($this->newdata_att)) {
										echo "attendance saved";
									}
									else {	
										echo "Error suring attendance saving";
									} 
								}
							}
							else {
								echo "Error during saving of data<br>";	
							}
						}
					}	
				}	
			}
		}	
	}	
}






/**
*	Checks if the provided holiday in string format is a public holiday as defined for the school.
*	returns	0	: Not a holiday
*				1	: Public Holiday	
*/
private function checkHoliday($date, $school_id)    {
 return(0);	// NOT a holiday
 	$activeSchoolYear = 1;
	$result = $this->Holiday->findAllBySchool_idAndActiveschoolyear($school_id, $activeSchoolYear);
	foreach ($result as $key => $value) {
		$t_list[] = $result[$key][Holiday][holidaylist];
	}
	
	$holidayList = str_split($t_list, 8);
	if (in_array($date, $holidayList)) {
		return(1);
	}
   else {
		return(0);   
   }
}




}
