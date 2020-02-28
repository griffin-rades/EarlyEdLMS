<?php namespace App\Controllers;

use App\Libraries\Aauth;

class HomePage extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentGradeList = $this->db->query('SELECT studentFirstName, studentLastName, grade FROM lms_studentInformation JOIN lms_students ON lms_studentInformation.studentID = lms_students.studentID');

		$studentInfo = $studentGradeList->getResult();

		$data['studentGradeList'] = $studentInfo;

		return view('teacherHome', $data);
	}
}

