<?php defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteInterfaceFormHelper
{
	/**
	 * @var TextHelper
	 */
	private $th;
	
	/**
	 * @var FormHelper
	 */
	private $fh;
	
	public function __construct()
	{
		$this->th = Loader::helper("text");
	}
	
	/**
	 * Turns an array in HTML attributes
	 * @param array $args
	 * @return string
	 */
	public function attributes(array $args)
	{
		$str = '';
		foreach($args as $k => $v) {
			if ($v) {
				$str .= ' '.$this->th->entities($k).'="'.$this->th->entities($v).'"';
			}
		}
		return $str;
	}
	
	public function nameToId($name)
	{
		return $id = trim(str_replace(array('[', ']'), array('_', ''), $name), '_');
	}
	
	public function tag($name, array $attr = array(), $inner = null, $escape_inner = true)
	{
		$attr = $this->attributes($attr);
		return "<{$name}{$attr}".
			($inner ? '>'.
				($escape_inner ? $this->th->entities($inner) : $inner).
			"</{$name}>" : '/>');
	}
	
	/**
	 * Generates a submit button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $formID The form this button will submit
	 * @param string $buttonAlign
	 * @param string $innerClass
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function submit($text, $formID = false, $buttonAlign = 'right',
		$innerClass = null, array $args = array())
	{
		
		if ($buttonAlign == 'right') {
			$innerClass .= ' ccm-button-v2-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' ccm-button-v2-left';
		}
	
		if (!$formID) {
			$formID = 'button';
		}
		
		$args['id'] = 'ccm-submit-'.$formID;
		$args['name'] = 'ccm-submit-'.$formID;
		$args['type'] = 'submit';
		$args['class'] = 'btn ccm-button-v2 '.$innerClass;
		$args['value'] = $text;
		
		return $this->tag('input', $args);
	}
	
	/**
	 * Generates a simple link button in the Concrete style
	 * @param string $text The text of the button
	 * @param string $href
	 * @param string $buttonAlign
	 * @param string $innerClass
	 * @param array $args Extra args passed to the link
	 * @return string
	 */
	public function button($text, $href, $buttonAlign = 'right',
		$innerClass = null, array $args = array())
	{
		if ($buttonAlign == 'right') {
			$innerClass .= ' ccm-button-v2-right';
		} else if ($buttonAlign == 'left') {
			$innerClass .= ' ccm-button-v2-left';
		}
		
		$args['href'] = $href;
		$args['class'] = 'btn '.$innerClass;
		return $this->tag('a', $args, $text);
	}
	
	public function jsbutton($id, $text, $classes = '', array $args = array())
	{
		$args['id'] = $id;
		$args['class'] = $classes . ' btn';
		return $this->tag('span', $args, $text);
	}
	
	/**
	 * Text tag
	 * @param string $name of the input
	 * @param string $label
	 * @param string $value
	 * @param string $help
	 * @param array $args attributes to be set in the input tag
	 */
	public function text($name, $label, $value = null, $help = null, array $args = array())
	{
		$id = $this->nameToId($name);
		$args = array_merge(array('type' => 'text', 'name' => $name, 'id' => $name, 'value' => $value), $args);
		ob_start();
		?>
		<div class="clearfix">
			<?=$this->tag('label', array('for' => $name), $label, false)?>
			<div class="input">
				<?=$this->tag('input', $args)?>
				<?if($help):?>
				<?=$this->tag('span', array('class' => 'help-block'), $help)?>
				<?endif?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Textarea tag
	 * @param string $name of the input
	 * @param string $label
	 * @param string $value
	 * @param string $help
	 */
	public function textarea($name, $label, $value = null, $help = null)
	{
		$id = $this->nameToId($name);
		ob_start();
		?>
		<div class="clearfix">
			<?=$this->tag('label', array('for' => $name), $label, false)?>
			<div class="input">
				<?=$this->tag('textarea', array('name' => $name, 'id' => $name, 'rows' => 3), $value)?>
				<?if($help):?>
				<?=$this->tag('span', array('class' => 'help-block'), $help)?>
				<?endif?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	
	public function radios($name, $label, array $options, $selected = null, $help = null)
	{
		$id = $this->nameToId($name);
		ob_start();
		?>
		<div class="clearfix">
			<?=$this->tag('label', array('id' => $id), $label, false)?>
			<div class="input">
				<ul class="inputs-list">
					<?foreach ($options as $value => $text):
					$args = array(
						'name' => $name,
						'type' => 'radio',
						'value' => $value
					);
					if ($selected !== null && $value == $selected) {
						$args['checked'] = 'checked'; 
					}
					?>
					<li><label>
						<?=$this->tag('input', $args)?>
						<?=$this->tag('span', array(), $text)?>
					</label></li>
					<?endforeach?>
				</ul>
				<?if($help):?>
				<?=$this->tag('span', array('class' => 'help-block'), $help)?>
				<?endif?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	
	public function inlineInputs($label, $inputs, $help = null)
	{
		ob_start();
		?>
		<div class="clearfix">
			<?=$this->tag('label', array('id' => $id), $label, false)?>
			<div class="input">
				<div class="inline-inputs">
					<?=$inputs?>
				</div>
				<?if($help):?>
				<?=$this->tag('span', array('class' => 'help-block'), $help)?>
				<?endif?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
