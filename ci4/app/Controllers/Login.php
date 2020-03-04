<?php namespace App\Controllers;

use App\Libraries\Aauth;

class Login extends BaseController{
	/**
	 * login User
	 *
	 * Using the login form data the user is logged in
	 *
	 * @return string, array
	 */
  function loginUser(){

    $data = array();
    $this->aauth = new Aauth();
    $data['aauth'] = $this->aauth;
    $data['email'] = $this->request->getVar('userEmail');
    $data['password'] = $this->request->getVar('userPassword');

    if($this->aauth->login($this->request->getVar('userEmail'), $this->request->getVar('userPassword'), $this->request->getVar('rememberMe'))){

		$studentNameList = $this->db->query('SELECT lastName, firstName, (SUM(assignGrade.points)/SUM(assignment.maxPoints)) * 100 AS average FROM assignGrade JOIN lms_students ON assignGrade.studentID = lms_students.id JOIN assignment ON assignGrade.assignmentID = assignment.id WHERE assignGrade.classID = '. "'". $this->aauth->getUserVar('classID') . "'" . 'GROUP BY assignGrade.studentID');

		$query = 'SELECT lms_class.classTitle FROM lms_class WHERE lms_class.id = ';
		$query .= strVal($this->aauth->getUserVar('classID'));

		$teacherClass = $this->db->query($query);

		$studentInfo = $studentNameList->getResult();
		$teacherClassInfo = $teacherClass->getResult();

		$data['studentNameList'] = $studentInfo;
		$data['teacherClass'] = $teacherClassInfo;

		return view('teacherHome', $data);
	}else{
		$data['errors'] = $this->aauth->printErrors('<br />', true);
    	return view('loginTeacher', $data);
	}
  }
}
