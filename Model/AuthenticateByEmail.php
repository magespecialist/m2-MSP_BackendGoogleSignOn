<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Security\Model\AdminSessionsManager;
use Magento\User\Model\User;
use MSP\GoogleSignOn\Model\ResourceModel\GetUsernameByEmail;

class AuthenticateByEmail
{
    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var User
     */
    private $adminUser;

    /**
     * @var GetUsernameByEmail
     */
    private $getUsernameByEmail;

    /**
     * @var AdminSessionsManager
     */
    private $adminSessionsManager;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @param GetUsernameByEmail $getUsernameByEmail
     * @param Session $authSession
     * @param AdminSessionsManager $adminSessionsManager
     * @param ManagerInterface $eventManager
     * @param User $adminUser
     */
    public function __construct(
        GetUsernameByEmail $getUsernameByEmail,
        Session $authSession,
        AdminSessionsManager $adminSessionsManager,
        ManagerInterface $eventManager,
        User $adminUser
    ) {
        $this->authSession = $authSession;
        $this->adminUser = $adminUser;
        $this->getUsernameByEmail = $getUsernameByEmail;
        $this->adminSessionsManager = $adminSessionsManager;
        $this->eventManager = $eventManager;
    }

    /**
     * Authenticate admin user by email
     *
     * @param string $email
     * @throws NoSuchEntityException
     */
    public function execute(string $email): void
    {
        $username = $this->getUsernameByEmail->execute($email);
        if (empty($username)) {
            $this->adminUser->unsetData();
        }

        $user = $this->adminUser->loadByUsername($username);
        $this->authSession->setUser($user);
        $this->authSession->processLogin();

        $this->eventManager->dispatch(
            'admin_user_authenticate_after',
            ['username' => $username, 'password' => '', 'user' => $user, 'result' => true]
        );

        if ($this->authSession->isLoggedIn()) {
            $this->adminSessionsManager->processLogin();
            $this->authSession->refreshAcl();
        }
    }
}
