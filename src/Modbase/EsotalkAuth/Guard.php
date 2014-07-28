<?php namespace Modbase\EsotalkAuth;

use Illuminate\Auth\Guard as LaravelGuard;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserProviderInterface;

class Guard extends LaravelGuard {

	/**
	 * Create a new authentication guard.
	 *
	 * @param  \Illuminate\Auth\UserProviderInterface  $provider
	 * @return void
	 */
	public function __construct(UserProviderInterface $provider)
	{
		$this->provider = $provider;

		// Dirty hack to be able to use the ETSession class
		define('IN_ESOTALK', true);

		$this->etSession =  new ETSession;
	}

	/**
	 * Determine if the current user is authenticated.
	 *
	 * @return bool
	 */
	public function check()
	{
		return ! is_null($this->user());
	}

	/**
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public function guest()
	{
		return is_null($this->user());
	}

	/**
	 * Get the currently authenticated user.
	 *
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function user()
	{
		if ($this->loggedOut) return;

		// If we have already retrieved the user for the current request we can just
		// return it back immediately. We do not want to pull the user data every
		// request into the method because that would tremendously slow the app.
		if ( ! is_null($this->user))
		{
			return $this->user;
		}

		$id = $this->etSession->userId();

		// First we will try to load the user using the identifier in the session if
		// one exists. Otherwise we will check for a "remember me" cookie in this
		// request, and if one exists, attempt to retrieve the user using that.
		$user = null;

		if ( ! is_null($id))
		{
			list($id, $remember) = $id;
			$user = $this->provider->retrieveByID($id);
			$this->setLoginCookie($user, $remember);
		}

		return $this->user = $user;
	}

	/**
	 * Log a user into the application.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 */
	public function login(UserInterface $user, $remember = false)
	{
		$this->setLoginCookie($user, $remember);

		// If we have an event dispatcher instance set we will fire an event so that
		// any listeners will hook into the authentication events and run actions
		// based on the login and logout events fired from the guard instances.
		if (isset($this->events))
		{
			$this->events->fire('auth.login', array($user, $remember));
		}

		$this->setUser($user);
	}
	
	/**
	 * Set or refresh our cookies.
	 * 
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 */
	protected function setLoginCookie(UserInterface $user, $remember = false)
	{
		$username = $user->getUsername();
		$password = $user->getAuthPassword();

		$this->etSession->login($username, $password, $remember);
	}

	/**
	 * Log the given user ID into the application.
	 *
	 * @param  mixed  $id
	 * @param  bool   $remember
	 * @return \Illuminate\Auth\UserInterface
	 */
	public function loginUsingId($id, $remember = false)
	{
		$user = $this->provider->retrieveByID($id);

		return $this->login($user, $remember);
	}

	/**
	 * Remove the user data from the session and cookies.
	 *
	 * @return void
	 */
	protected function clearUserDataFromStorage()
	{
		$this->etSession->logout();
	}
}
