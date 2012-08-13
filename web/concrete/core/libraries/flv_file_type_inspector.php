<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_FlvFileTypeInspector extends FileTypeInspector {
	
	public function inspect($fv) {
		
		$path = $fv->getPath();
		$at1 = FileAttributeKey::getByHandle('duration');
		$at2 = FileAttributeKey::getByHandle('width');
		$at3 = FileAttributeKey::getByHandle('height');
		
		$fp = @fopen($path,'r');
		@fseek($fp,27);
		$onMetaData = fread($fp,10);
		
		//if ($onMetaData != 'onMetaData') exit('No meta data available in this file! Fix it using this tool: http://www.buraks.com/flvmdi/');
		
		@fseek($fp,16,SEEK_CUR);
		$duration = array_shift(unpack('d',strrev(fread($fp,8))));
		
		@fseek($fp,8,SEEK_CUR);
		$width = array_shift(unpack('d',strrev(fread($fp,8))));
		
		@fseek($fp,9,SEEK_CUR);
		$height = array_shift(unpack('d',strrev(fread($fp,8))));
		
		$fv->setAttribute($at1, $duration);
		$fv->setAttribute($at2, $width);
		$fv->setAttribute($at3, $height);
				
	}
	

}