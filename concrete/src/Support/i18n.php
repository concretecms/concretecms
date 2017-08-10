<?php

/*
 * This file contains calls to t(), tc() and t2() functions to let the translation system know about strings to be translated.
 * It is never loaded or executed.
 */
return;

// To translate the value of the VERSION_INITIAL_COMMENT constant
t('Initial Version');

// To translate the names of the autentication types
// @see \Concrete\Core\Package\StartingPointPackage::add_users
tc('AuthenticationType', 'Standard');
tc('AuthenticationType', 'concrete5.org');
tc('AuthenticationType', 'Facebook');
tc('AuthenticationType', 'Twitter');
tc('AuthenticationType', 'Google');
