<?php

namespace Drupal\adbt_routing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sample controller.
 */
class SampleController extends ControllerBase {

  /**
   * Returns a render-able array for the page.
   */
  public function content() {
    $markup = 'Hello World!';

    $build = [
      '#markup' => $markup,
    ];

    return $build;
  }

  /**
   * Returns a render-able array for the page.
   */
  public function userParameter($user = NULL) {
    if ($user instanceof User) {
      $markup = 'User parameter an instance of Drupal\user\Entity\User with username: ' . $user->label();
    }
    else {
      $markup = 'User parameter value: ' . $user;
    }

    $build = [
      '#markup' => $markup,
    ];

    return $build;
  }

  /**
   * Returns a render-able array for the page.
   */
  public function multipleParameter($user = NULL, $message = NULL) {

    $markup = 'User parameter numeric value: ' . $user;
    $markup .= '<br />Message parameter alpha-numeric value: ' . $message;

    $build = [
      '#markup' => $markup,
    ];

    return $build;
  }

  /**
   * Returns a render-able array for the page.
   */
  public function extraParameter(
    RouteMatchInterface $route_match,
    Request $request = NULL,
    $new_param = NULL
  ) {

    $route_name = $route_match->getRouteName();
    $session_id = $request->getSession()->getId();

    $markup = 'Extra paramter: ' . $new_param;
    $markup .= '<br />Route Name: ' . $route_name;
    $markup .= '<br />Request Session: ' . $session_id;

    $build = [
      '#markup' => $markup,
    ];

    return $build;
  }

  /**
   * Returns a render-able array for the page.
   */
  public function dynamic(RouteMatchInterface $route_match) {
    $markup = 'Hello Dynamic World!';

    $route_name = $route_match->getRouteName();
    $markup .= '<br />Current Route Name: ' . $route_name;

    $build = [
      '#markup' => $markup,
    ];

    return $build;
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    // Check permissions and combine that with
    // any custom access checking needed.
    return $account->hasPermission('access content')
      ? AccessResult::allowed()
      : AccessResult::forbidden();
  }

}
