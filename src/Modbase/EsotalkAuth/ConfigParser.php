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

		return isset($variables[$key]) ? $variables[$key] : '';
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
			require_once $this->path.'core/config.defaults.php';
			require_once $this->path.'config/config.php';

			$variables = get_defined_vars();
			$this->variables = $variables['config'];
		}

		return $this->variables;
	}

}
