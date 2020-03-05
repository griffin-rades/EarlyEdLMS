<?php namespace App\Controllers;

use App\Libraries\Aauth;

class ParentInfo extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		$parentNamesList = $this->db->query('SELECT lms_students.firstName AS firstNameS, lms_students.lastName AS lastNameS, parent.firstName AS firstNameP, parent.lastName AS lastNameP FROM parentStudent JOIN lms_students ON parentStudent.studentID = lms_students.id JOIN parent ON parentStudent.parentID = parent.id WHERE parentStudent.classID = ' . $this->aauth->getUserVar('classID'));
		$parentInfo = $parentNamesList->getResult();

		$data['parentList'] = $parentInfo;

		return view('parents', $data);
	}
}

