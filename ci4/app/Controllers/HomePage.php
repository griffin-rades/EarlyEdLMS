<?php namespace App\Controllers;

use App\Libraries\Aauth;

class HomePage extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_studentInformation.grade FROM lms_students JOIN lms_studentInformation ON lms_students.id = lms_studentInformation.studentID WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));

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

