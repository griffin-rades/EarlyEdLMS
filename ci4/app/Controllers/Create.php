<?php namespace App\Controllers;

use App\Libraries\Aauth;

class Create extends BaseController{

	/**
	 * index
	 *
	 * loads the create account page
	 *
	 *
	 * @return string, array
	 */
	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		return view('createAccount', $data);
	}

	/**
	 * newUser
	 *
	 * Gets the form data when the create account form is submitted
	 *
	 *
	 * @return string, array
	 */
	function newUser(){
		$data = array();
		$this->aauth = new Aauth();

		$data['aauth'] = $this->aauth;

		$userEmail = $this->request->getVar('userEmail');
		$userPassword = $this->request->getVar('userPassword');
		$userName = $this->request->getVar('userName');
		$firstName = $this->request->getVar('firstName');
		$lastName = $this->request->getVar('lastName');

		$classID = rand(1,3); //pick random number to assign teacher to class.

		if($this->aauth->createUser($userEmail, $userPassword, $userName)){
			$data['success'] = "The account was successfuly created";
			$this->aauth->addMember(4, $this->aauth->getUserId($userEmail));
			$this->aauth->setUserVar('lastName',$lastName, $this->aauth->getUserId($userEmail));
			$this->aauth->setUserVar('firstName', $firstName, $this->aauth->getUserId($userEmail));
			$this->aauth->setUserVar('classID', $classID, $this->aauth->getUserId($userEmail));
		}else{
			$data['errors'] = $this->aauth->printErrors('<br />', true);
			return view('createAccount', $data);
		}

		return view('loginTeacher', $data);
	}

	/**
	 * createStudent
	 *
	 * Gets form data from the create student form
	 *
	 * @return string, array
	 */
	function createStudent(){
		$data = array();
		$this->aauth = new Aauth();

		$data['aauth'] = $this->aauth;

		$firstName = $this->request->getVar('firstName');
		$lastName = $this->request->getVar('lastName');
		$age = $this->request->getVar('age');

		$studentData = [
			'lastName' => $lastName,
			'firstName' => $firstName,
			'age' => $age,
			'classID' => $this->aauth->getUserVar('classID')
		];

		try{
			$this->studentModel->insert($studentData);
		} catch (\ReflectionException $e) {

		}

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		return view('students', $data);
	}

	/**
	 * create Assignment
	 *
	 * Creates an assignment for the class
	 *
	 * @return string, array
	 */
	function createAssignment(){
		$data = array();
		$this->aauth = new Aauth();
		$data['db'] = $this->db;

		$data['aauth'] = $this->aauth;

		$assignmentData = [
			'classID' => $this->aauth->getUserVar('classID'),
			'title' => $this->request->getVar('assignTitle'),
			'description' => $this->request->getVar('assignDesc'),
			'maxPoints' => $this->request->getVar('pointSlider')
		];

		try{
			$this->assignmentModel->insert($assignmentData);
		}catch (\ReflectionException $e){

		}

		$assignmentList = $this->db->query('SELECT title, description, maxPoints, id FROM assignment WHERE classID = ' . $this->aauth->getUserVar('classID'));
		$list = $assignmentList->getResult();

		$data['assignList'] = $list;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		return view('grades', $data);
	}
}
