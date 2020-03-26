<?php

/**
 * This file is part of the KrystalCode/Toggle package.
 *
 * (c) Dimitris Bozelos <dbozelos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KrystalCode\Toggle;

/**
 * Trait that facilitates toggling ON or OFF method execution.
 *
 * To use the toggle interceptor add the trait to your class and provide an
 * implementation of the `toggleMethods` method. You can then call any method
 * prefixing it with `toggle` and the method will be called or not depending on
 * the toggle configured in the `toggleMethods` method.
 *
 * If the class already implements the `__call` magic method, incorporate the
 * `__doCall` method provided by this trait into the `__call` method's logic.
 * Most common usage would be to call `__doCall` at the end of `__call`.
 */
trait InterceptorTrait
{
    /**
     * Returns the methods that should be intercepted by the toggle.
     *
     * @return array
     *   An associative array describing the methods that should be intercepted
     *   by the toggle. Based on the following format, methods will be called
     *   only if the toggle evaluates ON for all features. Right now, it only
     *   supports some of the quick framework integration toggles: yii1, yii2,
     *   symfony2 and drupal. Example:
     *   [
     *       'methodA' => [
     *           'featureA' => [
     *               'provider' => 'symfony',
     *               'value' => true,
     *           ],
     *           'featureB' => [
     *               'provider' => 'drupal',
     *               'value' => $onValue,
     *           ],
     *       ],
     *       'methodB' => [
     *           'featureA' => [
     *               'provider' => 'symfony',
     *               'value' => true,
     *           ],
     *       ],
     *  ]
     */
    abstract protected function toggleMethods();

    /**
     * Intercepts calls for methods starting with the `toggle` prefix.
     *
     * The method is then called if the toggle evaluates ON based on the
     * method's toggle configuration provided by the classes `toggleMethods`
     * implementation.
     *
     * @param string $method The method being called.
     * @param mixed  $args   The arguments the method is being called with.
     *
     * @throws \InvalidArgumentException
     *   When the called method is not configured to be handled by the toggle.
     */
    public function __call($method, $args)
    {
        $this->__doCall($method, $args);
    }

    /**
     * Intercepts calls for methods starting with the `toggle` prefix.
     *
     * The method is then called if the toggle evaluates ON based on the
     * method's toggle configuration provided by the classes `toggleMethods`
     * implementation.
     *
     * Made available in addition to `__call` for use when the class already has
     * a `__call` method implementation.
     *
     * @param string $method The method being called.
     * @param mixed  $args   The arguments the method is being called with.
     *
     * @throws \InvalidArgumentException
     *   When the called method is not configured to be handled by the toggle.
     */
    protected function __doCall($method, $args)
    {
        if (strpos($method, 'toggle') !== 0) {
            return;
        }

        $method = lcfirst(substr($method, 6));
        $methods = $this->toggleMethods();
        if (!in_array($method, array_keys($methods))) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" method is not configured to be handled by the toggle.',
                    $method
                )
            );
        }

        foreach ($methods[$method] as $feature => $config) {
            if (!Toggle::{$config['provider']}($feature, $config['value'])) {
                return;
            }
        }

        call_user_func_array(array($this, $method), $args);
    }
}
