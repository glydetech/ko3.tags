<?php defined('SYSPATH') or die('No direct script access.');

/**
* Tag handling class
* @package    Tags
* @author     Andrew Clarke
* @copyright  (c) 2010 Andrew Clarke
*/ 
class Tag
{

	/**
	* @var string The tag name
	*/
	public $name;
	
	/**
	* @var array Array of attributes
	*/
	public $attributes = array();
	
	/**
	* @var void Contents of the tag
	*/
	public $content;
	
	// Factory method
	
	/**
	* Static factory method
	*
	* @param string tag name
	* @param array arry of attributes
	* @param void tag content
	* @return Tag
	*/
	public static function factory($name = NULL, array $attributes = NULL, $content = NULL)
	{
		return new Tag($name, $attributes, $content);
	}
	
	// Constructor method
	
	/**
	* Constructor method
	*
	* @param string tag name
	* @param array arry of attributes
	* @param void tag content
	* @return Tag
	*/
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
	
	/**
	* Set the tag name
	*
	* @param string tag name
	* @return Tag
	*/
	public function set_name($name)
	{
		$this->name = $name;
		return $this;
	}
	
	/**
	* Add attributes to the tag
	*
	* @param void array of attributes
	* @return Tag
	*/
	public function add_attributes(array $attributes)
	{
		$this->attributes += $attributes;
		return $this;
	}
	
	/**
	* Add content to the tag
	*
	* @param void The tag content
	* @return Tag
	*/
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
	* @return string
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

	/**
	* Returns or outputs the tag
	*
	* @param boolean echo the result
	* @return string
	*/
	public function render($output = false)
	{
		$result = '';

		// Opening tag
		if ($this->name)
		{
			// Name
			$result .= '<';
			if (strtolower($this->name) == 'doctype')
			{
				$result .= '!';
			}
			$result .= $this->name;

			// Attributes
			$result .= Tag::attributes($this->attributes);

			// Close
			if (empty($this->content) AND strtolower($this->name) != 'doctype')
			{
				$result .= ' />';
			}
			else
			{
				$result .= '>';
			}
			$result .= "\n";
		}

		// Content
		if (!empty($this->content))
		{
			if (is_array($this->content))
			{
				foreach ($this->content as $value)
				{
					$result .= $value;
				}
			}
			else
			{
				$result .= $this->content;
			}
		}

		// Closing tag
		if ($this->name) {
			if (! empty($this->content))
			{
			  $result .= '</'.$this->name.'>';
			  $result .= "\n";
			}
		}

		// Output
		if ($output)
		{
			echo $result;
		}

		// Result
		return $result;
	}

	/**
	* Compiles an array of HTML attributes into an attribute string.
	*
	* @param array Attribute list
	* @return string
	*/
	public static function attributes(array $attributes = NULL)
	{
		// Check for empty array
		if (empty($attributes))
			return '';
		// Sort the attributes
		$sorted = array();
		foreach (HTML::$attribute_order as $key)
		{
			if (isset($attributes[$key]))
			{
				// Add the attribute to the sorted list
				$sorted[$key] = $attributes[$key];
			}
		}
		// Combine the sorted attributes
		$attributes = $sorted + $attributes;
		// Compile output
		$compiled = '';
		foreach ($attributes as $key => $value)
		{
			// Just display attribute names if value is null
			if ($value === NULL)
			{
				$compiled .= ' '.$key;
				continue;
			}
			// Display attribute name and open value
			$compiled .= ' ';
			if (! is_int($key))
			{
				$compiled .= $key.'=';
			}
			// Handle value array
			if (is_array($value))
			{
				foreach ($value as $v)
				{
					// For null values, just compile the keys (eg. class)
					if (is_null($v))
					{
						$value = implode(' ', array_keys($value));
					}
					// Join values with ';'
					else
					{
						$value = implode(';', $value);
					}
					// break out of loop
					break;
				}
			}
			// Display attribute
			$compiled .= '"'.htmlspecialchars($value, ENT_QUOTES, Kohana::$charset).'"';
		}

		return $compiled;
	}

} // End Tag