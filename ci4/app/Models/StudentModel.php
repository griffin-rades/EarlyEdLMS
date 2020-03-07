<?php namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model{
	protected $table      = 'lms_students';
	protected $primaryKey = 'id';

	protected $returnType = 'array';
	protected $useSoftDeletes = true;

	protected $allowedFields = ['firstName', 'lastName', 'age', 'classID', 'info'];

	protected $useTimestamps = false;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}
