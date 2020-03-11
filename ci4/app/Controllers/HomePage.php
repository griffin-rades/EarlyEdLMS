<?php namespace App\Controllers;

use App\Libraries\Aauth;

class HomePage extends BaseController{

	/**
	 * index
	 *
	 * This is what displays the teacher home page after the teacher logs in or uses the home tab of the navigation.
	 *
	 * @return string, array
	 */
	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentNameList = $this->db->query('SELECT lastName, firstName, (SUM(assignGrade.points)/SUM(assignment.maxPoints)) * 100 AS average FROM assignGrade JOIN lms_students ON assignGrade.studentID = lms_students.id JOIN assignment ON assignGrade.assignmentID = assignment.id WHERE assignGrade.classID = '. "'". $this->aauth->getUserVar('classID') . "'" . 'GROUP BY assignGrade.studentID');

		$query = 'SELECT lms_class.classTitle FROM lms_class WHERE lms_class.id = ';
		$query .= strVal($this->aauth->getUserVar('classID'));

		$teacherClass = $this->db->query($query);

		$studentInfo = $studentNameList->getResult();
		$teacherClassInfo = $teacherClass->getResult();


		$data['studentNameList'] = $studentInfo;
		$data['teacherClass'] = $teacherClassInfo;

		return view('teacherHome', $data);
	}
}

