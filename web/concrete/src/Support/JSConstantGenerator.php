<?php
namespace Concrete\Core\Support;

class JSConstantGenerator
{
	private $_constants = array();

	private $_register = null;


	public function __construct ( array $constants = array() )
	{
		$this->setConstants($constants);
	}

	public function setConstants( array $constants = array() )
	{
		$this->_constants = $constants;
		$this->_register = null;
	}

    public function scanSourceTree( $sourceDir )
    {
        $fh = \Core::make( 'helper/file' );

        echo "Scanning $sourceDir \n";

        $list = $fh->getDirectoryContents( $sourceDir, array(), true );

        foreach ( $list as $file )
        {
            if ( !preg_match ( '/\.php$/', $file ) ) continue;

            $content = $fh->getContents($file);
            if ( !preg_match( ',^\s*const\s*.*\s*//!<\s*@javascript-exported,m', $content)) continue;

            $ns = '';
            $class = '';
            $var = '';

            $content = str_replace( "\n\r", "\n", $content );
            $ln = 0;
            foreach ( explode( "\n", $content ) as $line )
            {
                $ln ++;
                if ( preg_match( '/\s*namespace .*\;/', $line ) ) $ns = preg_replace( ',\s*namespace\s+([^ -;]+);.*$,', '\1', $line );

                if ( preg_match( '/^\s*class.*/', $line ) )       $class = preg_replace( ',\s*class\s+([^ -;\{]+).*$,', '\1', $line );

                if ( preg_match( ',^\s*const\s*.*\s*//!<\s*@javascript-exported,', $line))
                {
                    $var = preg_replace( ',\s*const\s+([^ ]+)\s*=.*$,', '\1', $line );
                    $const = "$ns\\$class::$var";
                    $this->addConstant($const);
                }
            }
        }

        
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

    public function getConstantList() { return $this->_constants; }

	public function getRegister()
	{
		if ( is_null($this->_register) )
		{
			$this->_register = $this->_populateRegister();
		}
		return $this->_register;
	}


	public function render( $path = 'Concrete' )
	{
		$register = $this->getRegister();

        $extract = $register;
        if ( $path )
        {
            $pathArray = preg_split( ',\\\\|::,', $path );
            while ( count($pathArray) )
            {
                $key = '';
				while ( '' == $key && count($pathArray) ) $key = array_shift($pathArray);
                if ( $key ) $extract = $register[$key];
            }
        }


		return json_encode( $extract, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT );
	}
}
