<?php
namespace Concrete\Core\Authentication;

use User;

Interface AuthenticationTypeControllerInterface
{

    /**
     * Method used to verify the user and log them in.
     * Returning user will cause finishAuthentication to run, otherwise it's expected that the subclass manage completion.
     *
     * @throws AuthenticationTypeFailureException
     * @return \User|null
     */
    public function authenticate();

    /**
     * Method used to clean up.
     * This method must be defined, if it isn't needed, leave it blank.
     *
     * @param \User $u
     * @return void
     */
    public function deauthenticate(User $u);

    /**
     * Test user authentication status.
     *
     * @param \User $u
     * @return bool Returns true if user is authenticated, false if not
     */
    public function isAuthenticated(User $u);

    /**
     * Create a cookie hash to identify the user indefinitely
     *
     * @param \User $u
     * @return string Unique hash to be used to verify the users identity
     */
    public function buildHash(User $u);

    /**
     * Verify cookie hash to identify user.
     *
     * @param        $u User object requesting verification.
     * @param string $hash
     * @return bool returns true if the hash is valid, false if not
     */
    public function verifyHash(User $u, $hash);

    /**
     * tasks to finalize authentication, call on login events etc
     *
     * @param \User $u
     * @return void
     */
    public function completeAuthentication(User $u);
}
