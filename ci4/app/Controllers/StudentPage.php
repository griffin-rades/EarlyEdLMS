<?php namespace App\Controllers;

use App\Libraries\Aauth;

class StudentPage extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		return view('students', $data);
	}
}

