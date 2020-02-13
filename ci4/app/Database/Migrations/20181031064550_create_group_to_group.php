<?php
/**
 * CodeIgniter-Aauth
 *
 * Aauth is a User Authorization Library for CodeIgniter 4.x, which aims to make
 * easy some essential jobs such as login, permissions and access operations.
 * Despite ease of use, it has also very advanced features like grouping,
 * access management, public access etc..
 *
 * @package   CodeIgniter-Aauth
 * @author    Emre Akay
 * @author    Raphael "REJack" Jackstadt
 * @copyright 2014-2019 Emre Akay
 * @license   https://opensource.org/licenses/MIT   MIT License
 * @link      https://github.com/emreakay/CodeIgniter-Aauth
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Config\Aauth as AauthConfig;

/**
 * Create group to group table
 *
 * @package CodeIgniter-Aauth
 *
 * @codeCoverageIgnore
 */
class Migration_create_group_to_group extends Migration
{
	/**
	 * Create Table
	 *
	 * @return void
	 */
	public function up()
	{
		$config = new AauthConfig();
		$this->forge->addField([
			'group_id'    => [
				'type'       => 'INT',
				'constraint' => 11,
				'unsigned'   => true,
			],
			'subgroup_id' => [
				'type'       => 'INT',
				'constraint' => 11,
				'unsigned'   => true,
			],
		]);
		$this->forge->addKey(['group_id', 'subgroup_id'], true);
		$this->forge->addForeignKey('group_id', $config->dbTableGroups, 'id', false, 'CASCADE');
		$this->forge->addForeignKey('subgroup_id', $config->dbTableGroups, 'id', false, 'CASCADE');
		$this->forge->createTable($config->dbTableGroupToGroup, true);
	}

	//--------------------------------------------------------------------

	/**
	 * Drops Table
	 *
	 * @return void
	 */
	public function down()
	{
		$config = new AauthConfig();
		$this->forge->dropForeignKey($config->dbTableGroupToGroup, $config->dbTableGroupToGroup . '_group_id_foreign');
		$this->forge->dropForeignKey($config->dbTableGroupToGroup, $config->dbTableGroupToGroup . '_subgroup_id_foreign');
		$this->forge->dropTable($config->dbTableGroupToGroup, true);
	}
}
