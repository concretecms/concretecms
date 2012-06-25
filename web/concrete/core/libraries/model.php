<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
* Concrete Model Class
* The model class extends the ADOdb active record class, allowing items that inherit from it to use the automatic create, updating, read and delete functionality it provides.
* @link http://phplens.com/lens/adodb/docs-active-record.htm
* @author Andrew Embler <andrew@concrete5.org>
* @link http://www.concrete5.org
* @package Utilities
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
class Concrete5_Library_Model extends ADOdb_Active_Record {


	public function __construct() {
	 	$db = Loader::db();
	 	parent::__construct();
	}		 


}