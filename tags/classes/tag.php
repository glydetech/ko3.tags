<?php defined('SYSPATH') or die('No direct script access.');

/**
* Tag handling class
* @package    Tags
* @author     Andrew Clarke
* @copyright  (c) 2010 Andrew Clarke
*/ 
class Tag
{

	public $name;
	public $attributes = array();
	public $content;
	
	public static function factory($name = NULL, array $attributes = NULL, $content = NULL)
	{
		return new Tag($name, $attributes, $content);
	}
	
	public function __construct($name = NULL, array $attributes = NULL, $content = NULL)
	{
		if ($name !== NULL)
			$this->set_name($name);
		if ($attributes !== NULL)
			$this->add_attributes($attributes);
		if ($content !== NULL)
			$this->add_content($content);
	}
	
	// Setters
	
	public function set_name($name)
	{
		$this->name = $name;
		return $this;
	}
	
	public function add_attributes($attributes)
	{
		$this->attributes += $attributes;
		return $this;
	}
	
	public function add_content($content)
	{
		if (empty($this->content))
		{
			$this->content = $content;
		}
		else
		{
			if (!is_array($this->content))
			{
				$this->content = array($this->content, $content);
			}
			else
			{
				$this->content[] = $content;
			}
		}
		return $this;
	}
	
	// Magic methods

	/**
	 * Magic method, returns the output of render(). If any exceptions are
	 * thrown, the exception output will be returned instead.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			// Display the exception message
			Kohana::exception_handler($e);
			return '';
		}
	}

	// Render method
	
	public function render($output = false)
	{
		//echo 'rendering '.$this->name.'<br />';
		// if (! empty($this->name))
		$result = '<'.$this->name;
		if (! empty($this->attributes))
		{
			$result .= Html::attributes($this->attributes);
		}
		
		if (empty($this->content))
		{
			$result .= ' />';
		}
		else
		{
			//if (!empty($this->attributes))
			//{
			//	$result .= ' ';
			//}
			$result .= '>';
			if (is_array($this->content))
			{
				//echo 'array<br/>';
				foreach ($this->content as $value)
				{
					$result .= $value;
				}
			}
			else
			{
				//echo 'single<br/>';
				$result .= $this->content;
			}
			$result .= '</'.$this->name.'>';
		}
		if ($output)
		{
			echo $result;
		}
		//echo 'returning '.$this->name.'<br />';
		return $result;
	}
	

} // End Tag