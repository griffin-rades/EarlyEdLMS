<?php namespace App\Models;

use CodeIgniter\Model;

class parentModel extends Model{
	protected $table      = 'parent';
	protected $primaryKey = 'id';

	protected $returnType = 'array';
	protected $useSoftDeletes = true;

	protected $allowedFields = ['firstName', 'lastName', 'email'];

	protected $useTimestamps = false;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}

