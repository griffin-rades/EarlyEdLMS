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

		$studentGradeList = $this->db->query('SELECT studentFirstName, studentLastName, grade FROM lms_studentInformation JOIN lms_students ON lms_studentInformation.studentID = lms_students.studentID');

		$studentInfo = $studentGradeList->getResult();

		$data['studentGradeList'] = $studentInfo;

		return view('teacherHome', $data);
	}else{
		$data['errors'] = $this->aauth->printErrors('<br />', true);
    	return view('loginTeacher', $data);
	}
  }
}
