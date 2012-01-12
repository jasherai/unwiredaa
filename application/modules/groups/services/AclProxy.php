<?php
/**
* Unwired AA GUI
*
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
*
* Licensed under the terms of the Affero Gnu Public License version 3
* (AGPLv3 - http://www.gnu.org/licenses/agpl.html) or our proprietory
* license available at http://www.unwired.at/license.html
*/

class Groups_Service_AclProxy extends Zend_Acl
{
    protected $_acl = null;

    public function __construct(Zend_Acl $acl)
    {
        $this->setAcl($acl);
    }

    /**
     * Getet the acl instance
     *
     * @return Zend_Acl $acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Set the acl instance
     *
     * @param Zend_Acl $acl
     * @return Groups_Service_AclProxy
     */
    public function setAcl(Zend_Acl $acl)
    {
        $this->_acl = $acl;
        return $this;
    }

        /**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  Zend_Acl_Role_Interface              $role
     * @param  Zend_Acl_Role_Interface|string|array $parents
     * @uses   Zend_Acl_Role_Registry::add()
     * @return Zend_Acl Provides a fluent interface
     */
    public function addRole($role, $parents = null)
    {
        return $this->getAcl()->addRole($role, $parents);
    }

    /**
     * Returns the identified Role
     *
     * The $role parameter can either be a Role or Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl_Role_Registry::get()
     * @return Zend_Acl_Role_Interface
     */
    public function getRole($role)
    {
        return $this->getAcl()->getRole($role);
    }

    /**
     * Returns true if and only if the Role exists in the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl_Role_Registry::has()
     * @return boolean
     */
    public function hasRole($role)
    {
        return $this->getAcl()->hasRole($role);
    }

    /**
     * Returns true if and only if $role inherits from $inherit
     *
     * Both parameters may be either a Role or a Role identifier. If
     * $onlyParents is true, then $role must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance DAG to determine whether $role
     * inherits from $inherit through its ancestor Roles.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @param  Zend_Acl_Role_Interface|string $inherit
     * @param  boolean                        $onlyParents
     * @uses   Zend_Acl_Role_Registry::inherits()
     * @return boolean
     */
    public function inheritsRole($role, $inherit, $onlyParents = false)
    {
        return $this->getAcl()->inheritsRole($role, $inherit, $onlyParents);
    }

    /**
     * Removes the Role from the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl_Role_Registry::remove()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeRole($role)
    {
        return $this->getAcl->removeRole($role);
    }

    /**
     * Removes all Roles from the registry
     *
     * @uses   Zend_Acl_Role_Registry::removeAll()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeRoleAll()
    {
        return $this->getAcl()->removeRoleAll();
    }

    /**
     * Adds a Resource having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing Resource from which the newly added Resource will inherit.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  Zend_Acl_Resource_Interface|string $parent
     * @throws Zend_Acl_Exception
     * @return Zend_Acl Provides a fluent interface
     */
    public function addResource($resource, $parent = null)
    {
        return $this->getAcl()->addResource($resource, $parent);
    }

    /**
     * Returns the identified Resource
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @throws Zend_Acl_Exception
     * @return Zend_Acl_Resource_Interface
     */
    public function get($resource)
    {
        return $this->getAcl()->get($resource);
    }

    /**
     * Returns true if and only if the Resource exists in the ACL
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @return boolean
     */
    public function has($resource)
    {
        return $this->getAcl()->has($resource);
    }

    /**
     * Returns true if and only if $resource inherits from $inherit
     *
     * Both parameters may be either a Resource or a Resource identifier. If
     * $onlyParent is true, then $resource must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance tree to determine whether $resource
     * inherits from $inherit through its ancestor Resources.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  Zend_Acl_Resource_Interface|string $inherit
     * @param  boolean                            $onlyParent
     * @throws Zend_Acl_Resource_Registry_Exception
     * @return boolean
     */
    public function inherits($resource, $inherit, $onlyParent = false)
    {
        return $this->getAcl()->inherits($resource, $inherit, $onlyParent);
    }

    /**
     * Removes a Resource and all of its children
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @throws Zend_Acl_Exception
     * @return Zend_Acl Provides a fluent interface
     */
    public function remove($resource)
    {
        return $this->getAcl()->remove($resource);
    }

    /**
     * Removes all Resources
     *
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeAll()
    {
        return $this->getAcl()->removeAll();
    }

    /**
     * Adds an "allow" rule to the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function allow($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null)
    {
        return $this->getAcl()->allow($roles, $resources, $privileges, $assert);
    }

    /**
     * Adds a "deny" rule to the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function deny($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null)
    {
        return $this->getAcl()->deny($roles, $resources, $privileges, $assert);
    }

    /**
     * Removes "allow" permissions from the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeAllow($roles = null, $resources = null, $privileges = null)
    {
        return $this->getAcl()->removeAllow($roles, $resources, $privileges);
    }

    /**
     * Removes "deny" restrictions from the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeDeny($roles = null, $resources = null, $privileges = null)
    {
        return $this->getAcl()->removeDeny($roles, $resources, $privileges);
    }

    /**
     * Performs operations on ACL rules
     *
     * The $operation parameter may be either OP_ADD or OP_REMOVE, depending on whether the
     * user wants to add or remove a rule, respectively:
     *
     * OP_ADD specifics:
     *
     *      A rule is added that would allow one or more Roles access to [certain $privileges
     *      upon] the specified Resource(s).
     *
     * OP_REMOVE specifics:
     *
     *      The rule is removed only in the context of the given Roles, Resources, and privileges.
     *      Existing rules to which the remove operation does not apply would remain in the
     *      ACL.
     *
     * The $type parameter may be either TYPE_ALLOW or TYPE_DENY, depending on whether the
     * rule is intended to allow or deny permission, respectively.
     *
     * The $roles and $resources parameters may be references to, or the string identifiers for,
     * existing Resources/Roles, or they may be passed as arrays of these - mixing string identifiers
     * and objects is ok - to indicate the Resources and Roles to which the rule applies. If either
     * $roles or $resources is null, then the rule applies to all Roles or all Resources, respectively.
     * Both may be null in order to work with the default rule of the ACL.
     *
     * The $privileges parameter may be used to further specify that the rule applies only
     * to certain privileges upon the Resource(s) in question. This may be specified to be a single
     * privilege with a string, and multiple privileges may be specified as an array of strings.
     *
     * If $assert is provided, then its assert() method must return true in order for
     * the rule to apply. If $assert is provided with $roles, $resources, and $privileges all
     * equal to null, then a rule having a type of:
     *
     *      TYPE_ALLOW will imply a type of TYPE_DENY, and
     *
     *      TYPE_DENY will imply a type of TYPE_ALLOW
     *
     * when the rule's assertion fails. This is because the ACL needs to provide expected
     * behavior when an assertion upon the default ACL rule fails.
     *
     * @param  string                                   $operation
     * @param  string                                   $type
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @throws Zend_Acl_Exception
     * @uses   Zend_Acl_Role_Registry::get()
     * @uses   Zend_Acl::get()
     * @return Zend_Acl Provides a fluent interface
     */
    public function setRule($operation, $type, $roles = null, $resources = null, $privileges = null,
                            Zend_Acl_Assert_Interface $assert = null)
    {
        return $this->getAcl()->setRule($operation, $type, $roles, $resources, $privileges, $assert);
    }


    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        $aclResult = $this->getAcl()->isAllowed($role, $resource, $privilege);

        if (!$role instanceof Users_Model_Admin || $aclResult) {
            return $aclResult;
        }

        $groupsAssigned = $role->getGroupsAssigned();

        foreach ($groupsAssigned as $groupId => $roleId) {
            $aclResult = $this->getAcl()->isAllowed('groups_group_' . $groupId, $resource, $privilege);
            if ($aclResult) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of registered roles.
     *
     * Note that this method does not return instances of registered roles,
     * but only the role identifiers.
     *
     * @return array of registered roles
     */
    public function getRoles()
    {
        return $this->getAcl()->getRoles();
    }

    /**
     * @return array of registered resources
     */
    public function getResources()
    {
        return $this->getAcl()->getResources();
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->getAcl(), $method), $args);
    }
}