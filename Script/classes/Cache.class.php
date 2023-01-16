<?php

namespace SocialKit;

class Cache
{
	use \SocialTrait\AddOn;

	private $type;
	private $id;
	private $data;
	private $dir;
	private $file;
	private $filename;
	private $output;
	private $start = false;
	private $end = false;

	function __construct()
	{
		$this->dir = 'cache/webdata';
		return $this;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function prepare()
	{
		$this->filename = $this->type . $this->id;
		$this->file = $this->dir . '/' . $this->filename . '.php';
	}

	public function exists()
	{
		if (file_exists($this->file))
		{
			return true;
		}

		return false;
	}

	public function get()
	{
		include_once($this->file);

		if (function_exists($this->filename))
		{
			$function = $this->filename;
			return $function();
		}

		return array();
	}

	public function create($arr=0)
	{
		$output = '<?php';
		$output .= "\n";
		$output .= 'function ' . $this->filename . '(){';
		$output .= "\n";
		$output .= '$get = array();';
		$output .= "\n";

		$this->createOutput();
		$output .= $this->output;

		$output .= 'return $get;';
		$output .= "\n";
		$output .= '}';
		$this->output = $output;
		
		if (file_exists($this->dir))
		{
			if (file_put_contents($this->file, $this->output))
			{
				return true;
			}
		}

		return false;
	}

	private function createOutput($index='$get', $arr=0)
	{
		if ($arr == 0) $arr = $this->data;

		foreach ($arr as $key => $value)
		{
			if (is_array($value))
			{
				$newIndex = $index . '[\'' . $key . '\']';
				$this->createOutput($newIndex, $value);
			}
			else
			{
				$this->output .= $index . '[\'' . $key . '\'] = \'' . $value . '\';';
				$this->output .= "\n";
			}
		}
	}

	public function preview()
	{
		return htmlspecialchars($this->output);
	}

	public function clear()
	{
		if (file_exists($this->file))
		{
			if (unlink($this->file))
			{
				return true;
			}
		}

		return false;
	}

	public function clearAll()
	{
		$files = glob($this->dir . '/*.php');

		foreach ($files as $file)
		{
			unlink($file);
		}

		return true;
	}

	public function fromAdminArea($boolean=false)
	{
		if ($boolean)
		{
			$this->dir = '../cache/webdata';
		}
	}
}