<?php
namespace Concrete\Core\Authentication;
use User;
Interface AuthenticationTypeControllerInterface {
	/**
	 * Method used to verify the user and log them in.
	 * No return value, this method should either throw an exception or log the user in.
	 */
	public function authenticate();

	/**
	 * Method used to clean up.
	 * This method must be defined, if it isn't needed, leave it blank.
	 */
	public function deauthenticate(User $u);

	/**
	 * Test user authentication status.
	 *
	 * @return	boolean	is the user authenticated?
	 */
	public function isAuthenticated(User $u);

	/**
	 * Create a cookie hash to identify the user indefinitely
	 *
	 * @return		Unique hash to be used to verify the users identity
	 */
	public function buildHash(User $u);

	/**
	 * Verify cookie hash to identify user.
	 *
	 * @param	$u		User object requesting verification.
	 * @param	$hash	String that contains any information relevant.
	 * @return	boolean	Is this hash valid?
	 */
	public function verifyHash(User $u, $hash);

	/**
	 * tasks to finalize authentication, call on login events etc
	 * @param User
	 * @return void
	*/
	public function completeAuthentication(User $u);
}