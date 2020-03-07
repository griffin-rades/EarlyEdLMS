<?php namespace App\Models;

use CodeIgniter\Model;

class parentStudentModel extends Model{
	protected $table      = 'parentStudent';
	protected $primaryKey = 'id';

	protected $returnType = 'array';
	protected $useSoftDeletes = true;

	protected $allowedFields = ['studentID', 'parentID', 'classID'];

	protected $useTimestamps = false;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}

