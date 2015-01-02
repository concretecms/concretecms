<?php
namespace Concrete\Core\Support;

class JSConstantGenerator
{
	private $_constants = array();

	private $_register = null;


	public function __construct ( $constants = array() )
	{
		// All vars listed here will be appended by Grunt into the Concrete.const JS Object
		$constants = array (

            # Used by the FileManager javascript API to add filters
			"\Concrete\Core\File\Type\Type::T_IMAGE",
			"\Concrete\Core\File\Type\Type::T_VIDEO",
			"\Concrete\Core\File\Type\Type::T_AUDIO",
			"\Concrete\Core\File\Type\Type::T_DOCUMENT",
			"\Concrete\Core\File\Type\Type::T_APPLICATION",
			"\Concrete\Core\File\Type\Type::T_UNKNOWN",

            "\Concrete\Controller\Search\Files::FILTER_BY_SIZE",
            "\Concrete\Controller\Search\Files::FILTER_BY_TYPE",
            "\Concrete\Controller\Search\Files::FILTER_BY_EXTENSION",
            "\Concrete\Controller\Search\Files::FILTER_BY_ADDED_DATE",
            "\Concrete\Controller\Search\Files::FILTER_BY_ADDED_TO_PAGE",
		);

		$this->setConstants($constants);
	}

	public function setConstants( array $constants )
	{
		$this->_constants = $constants;
		$this->_register = null;
	}

    public function addConstant( $const ) { $this->_constants[] = $const; }
    public function addConstants( array $consts ) { $this->_constants = array_merge( $this->_constants, $consts); }

	protected function _populateRegister()
	{
		$register = array();
		foreach ( $this->_constants as $constant )
		{
			$value = eval(" return $constant;");
			$pathArray = preg_split( ',\\\\|::,', $constant );

			$current = &$register;
			while ( count($pathArray) )
			{
				$key = '';
				while ( '' == $key && count($pathArray) ) $key = array_shift($pathArray);

				if ( count( $pathArray) )
				{
					if ( !array_key_exists($key, $current ) || !is_array( $current[$key] ) ) $current[$key] = array();
					$current = &$current[$key];
				}
				else
				{
					$current[$key] = $value;
					break;
				}
			}
		}

		return $register;
	}

	public function getRegister()
	{
		if ( is_null($this->_register) )
		{
			$this->_register = $this->_populateRegister();
		}
		return $this->_register;
	}


	public function render()
	{
		$register = $this->getRegister();
		return json_encode( $register['Concrete'], JSON_FORCE_OBJECT | JSON_PRETTY_PRINT );
	}
}
