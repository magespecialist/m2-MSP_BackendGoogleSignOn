<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Plugin;

use Exception;
use Magento\Backend\Controller\Adminhtml\Auth\Login;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use MSP\GoogleSignOn\Model\AuthenticateByEmail;
use MSP\GoogleSignOn\Model\CreateUserFromGoogleUserInfo;
use MSP\GoogleSignOn\Model\GetGoogleUserInfo;
use MSP\GoogleSignOn\Model\IsEnabled;

class AuthenticateWithGoogle
{
    /**
     * @var GetGoogleUserInfo
     */
    private $getGoogleUserInfo;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AuthenticateByEmail
     */
    private $authenticateByEmail;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var IsEnabled
     */
    private $isEnabled;

    /**
     * @var CreateUserFromGoogleUserInfo
     */
    private $createUserFromGoogleUserInfo;

    /**
     * @param RequestInterface $request
     * @param IsEnabled $isEnabled
     * @param GetGoogleUserInfo $getGoogleUserInfo
     * @param AuthenticateByEmail $authenticateByEmail
     * @param Session $authSession
     * @param ManagerInterface $eventManager
     * @param CreateUserFromGoogleUserInfo $createUserFromGoogleUserInfo
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        IsEnabled $isEnabled,
        GetGoogleUserInfo $getGoogleUserInfo,
        AuthenticateByEmail $authenticateByEmail,
        Session $authSession,
        ManagerInterface $eventManager,
        CreateUserFromGoogleUserInfo $createUserFromGoogleUserInfo,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->getGoogleUserInfo = $getGoogleUserInfo;
        $this->request = $request;
        $this->authenticateByEmail = $authenticateByEmail;
        $this->eventManager = $eventManager;
        $this->messageManager = $messageManager;
        $this->authSession = $authSession;
        $this->isEnabled = $isEnabled;
        $this->createUserFromGoogleUserInfo = $createUserFromGoogleUserInfo;
    }

    /**
     * @param Login $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(Login $subject): void
    {
        if ($this->isEnabled->execute()) {
            $authCode = $this->request->getParam('code', '');
            if (!empty($authCode)) {
                try {
                    $googleUserInfo = $this->getGoogleUserInfo->execute($authCode);
                    $this->createUserFromGoogleUserInfo->execute($googleUserInfo);

                    $this->authenticateByEmail->execute($googleUserInfo->getEmail());

                    $this->eventManager->dispatch(
                        'backend_auth_user_login_success',
                        ['user' => $this->authSession]
                    );
                } catch (Exception $e) {
                    $this->eventManager->dispatch(
                        'backend_auth_user_login_failed',
                        ['user_name' => '', 'exception' => $e]
                    );

                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }
    }
}
