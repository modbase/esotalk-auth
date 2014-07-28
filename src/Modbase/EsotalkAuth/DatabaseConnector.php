<?php namespace Modbase\EsotalkAuth;

use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseConnector {

	/**
	 * The database connection factory.
	 *
	 * @var \Illuminate\Database\Connectors\ConnectionFactory
	 */
	protected $factory;

	/**
	 * The config parser instance.
	 *
	 * @var \Modbase\EsotalkAuth\ConfigParser
	 */
	protected $parser;

	/**
	 * Create a new database connector.
	 *
	 * @param  \Illuminate\Database\Connectors\ConnectionFactory  $factory
	 * @param  \Modbase\EsotalkAuth\ConfigParser  $parser
	 * @return void
	 */
	public function __construct(ConnectionFactory $factory, ConfigParser $parser)
	{
		$this->factory = $factory;
		$this->parser = $parser;
	}

	/**
	 * Get a database connection as configured.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function connection()
	{
		$config = array(
			'driver'    => 'mysql',
			'host'      => $this->parser->get('esoTalk.database.host'),
			'database'  => $this->parser->get('esoTalk.database.dbName'),
			'username'  => $this->parser->get('esoTalk.database.user'),
			'password'  => $this->parser->get('esoTalk.database.password'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => $this->parser->get('esoTalk.database.prefix'),
		);

		return $this->factory->make($config, 'esotalk-auth');
	}
}
