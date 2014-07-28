<?php namespace Modbase\EsotalkAuth;

class ConfigParser {

	/**
	 * The path to the Esotalk config file.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * All local variables from the configuration file.
	 *
	 * @var array
	 */
	protected $variables;

	/**
	 * Create a new config parser.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Get the value of the variable with the given key.
	 *
	 * @param  string  $key
	 * @return string
	 */
	public function get($key)
	{
		$variables = $this->variables();

		return isset($variables['config'][$key]) ? $variables['config'][$key] : '';
	}

	/**
	 * Get all variables that were defined in the configuration file.
	 *
	 * @return array
	 */
	protected function variables()
	{
		if ( ! isset($this->variables))
		{
			include $this->path.'config.defaults.php';
			include $this->path.'config.php';

			$this->variables = get_defined_vars();
		}

		return $this->variables;
	}

}
