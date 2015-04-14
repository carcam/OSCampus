<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\User;

use Oscampus\Api\Plan;
use Oscampus\Exception;
use Oscampus\User\User;

defined('_JEXEC') or die();

interface UserInterface
{
    /**
     * Load the selected User ID
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(User $parent);

    /**
     * Load a user from the username
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function loadByUsername(User $parent);

    /**
     * Load a user from the email address
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function loadByEmail(User $parent);

    /**
     * Create a new user. It is up to the system instances to perform
     * validation on the properties.
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function create(User $parent);

    /**
     * Update the user.
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function update(User $parent);

    /**
     * Validate the password for the user
     *
     * @param User   $parent
     * @param string $password
     *
     * @return bool
     */
    public function validate(User $parent, $password);

    /**
     * Log the user in
     *
     * @param User   $parent
     * @param string $password
     * @param bool   $force
     *
     * @return void
     * @throws Exception
     */
    public function login(User $parent, $password, $force = false);

    /**
     * Log out if possible
     *
     * @param User $parent
     *
     * @return void
     * @throws Exception
     */
    public function logout(User $parent);

    /**
     * Add user groups based on plans
     *
     * @param User  $parent
     * @param array $planCodes
     * @param bool  $replace   Clear all current plan groups
     *
     * @return void
     * @throws Exception
     */
    public function addGroups(User $parent, array $planCodes, $replace = false);

    /**
     * Remove groups from the user's profile based on the selected plans
     *
     * @param User  $parent
     * @param array $planCodes
     *
     * @return void
     * @throws Exception
     */
    public function removeGroups(User $parent, array $planCodes);

    /**
     * Get a human friendly version of group membership
     *
     * @param User $parent
     *
     * @return string
     */
    public function getGroupText(User $parent);
}
