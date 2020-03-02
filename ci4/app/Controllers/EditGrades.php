<?php namespace App\Controllers;

use App\Libraries\Aauth;

class EditGrades extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$assignmentList = $this->db->query('SELECT description, maxPoints, id FROM assignment WHERE classID = ' . $this->aauth->getUserVar('classID'));
		$list = $assignmentList->getResult();

		$data['assignList'] = $list;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		return view('grades', $data);
	}

	function gradeStudent(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		$assignmentList = $this->db->query('SELECT description, maxPoints, id FROM assignment WHERE classID = ' . $this->aauth->getUserVar('classID'));
		$list = $assignmentList->getResult();

		$data['assignList'] = $list;

		return view('grades', $data);

	}
}

