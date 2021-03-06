<?php
namespace Flowpack\Neos\FrontendUserManager\Controller;

/*                                                                                   *
 * This script belongs to the TYPO3 Flow package "Flowpack.Neos.FrontendUserManager".*
 *                                                                                   *
 *                                                                                   */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface;
use TYPO3\Flow\Security\Context as SecurityContext;
use TYPO3\Flow\Security\Authentication\TokenInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * Controller that handles the frontend logout
 */
class LogoutController extends AbstractBaseController {

	/**
	 * @var AuthenticationManagerInterface
	 * @Flow\Inject
	 */
	protected $authenticationManager;

	/**
	 * @return void
	 */
	public function indexAction() {
	}

	/**
	 * @return string
	 */
	public function logoutAction() {
		$this->isAuthenticated = NULL;
		/** @var $token TokenInterface */
		foreach ($this->securityContext->getAuthenticationTokens() as $token) {
			if ($token->getAuthenticationProviderName() === $this->settings['providerName']) {
				$token->setAuthenticationStatus(TokenInterface::NO_CREDENTIALS_GIVEN);
			}
		}

		$this->executeRedirect();
	}

	/**
	 * Executes a redirect to the configured node or to the site node if no node for redirect is set
	 *
	 * @return void
	 */
	protected function executeRedirect() {
		/** @var NodeInterface $node */
		$node = $this->request->getInternalArgument('__node');
		if ($node->getNodeType()->getName() === 'Flowpack.Neos.FrontendUserManager:Logout') {
			$redirectNode = $node->getProperty('redirect');
			if ($redirectNode === NULL) {
				$redirectNode = $node->getContext()->getCurrentSiteNode();
			}
			$uri = $this->helperService->getUriForNode($redirectNode, $this->controllerContext);
		} else {
			$uri = $this->helperService->getUriFromRequest($this->request);
		}
		$this->redirectToUri($uri);
	}

}