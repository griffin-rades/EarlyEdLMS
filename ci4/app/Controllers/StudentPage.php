<?php namespace App\Controllers;

use App\Libraries\Aauth;

class StudentPage extends BaseController{

	/**
	 * index
	 *
	 * This is called when the student tab is clicked in the nav bar.
	 *
	 * @return string, array
	 */
	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		return view('students', $data);
	}

	/**
	 * studentNote
	 *
	 * This is called when the teacher submits the form to give a student a note.
	 *
	 * @return string, array
	 */
	function studentNote(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		$studentNote = $this->request->getVar('textInfo');
		$studentID = $this->request->getVar('studentInfo');

		$this->db->query('UPDATE lms_students SET info = ' . "'" . $studentNote . "'" . ' WHERE id = ' . $studentID);

		$studentNameList = $this->db->query('SELECT lms_students.firstName, lms_students.lastName, lms_students.id, lms_students.info FROM lms_students WHERE lms_students.classID = ' . $this->aauth->getUserVar('classID'));
		$studentInfo = $studentNameList->getResult();

		$data['studentList'] = $studentInfo;

		return view('students', $data);
	}
}

