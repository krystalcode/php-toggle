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

use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * Provides easier syntax for calling Toggles.
 *
 * @author Dimitris Bozelos <dbozelos@gmail.com>
 */
class Toggle
{
    public static function params(array $input, $varName, $varValue = null)
    {
        $loader = new ConfigLoaderArray($input);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }

    public static function yaml($input, $varName, $varValue = null)
    {
        $loader = new ConfigLoaderYaml(new YamlParser(), $input);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }

    public static function php($input, $varName, $varValue = null)
    {
        $loader = new ConfigLoaderPhp($input);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }

    public static function ini($input, $varName, $varValue = null)
    {
        $loader = new ConfigLoaderIni($input);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }

    public static function yii1($varName, $varValue = null)
    {
        $loader = new ConfigLoaderArray(\Yii::app()->params['toggle']);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }

    public static function yii2($varName, $varValue = null)
    {
        $loader = new ConfigLoaderArray(\Yii::$app->params['toggle']);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }

    /**
     * @I Create a Symonfy bundle for better integration incl. Twig extension
     *    type     : improvement
     *    priority : normal
     *    labels   : integrations
     */
    public static function symfony2($varName, $varValue = null)
    {
        $input = realpath(__DIR__ . '/../../../../app/config/toggle.yml');
        $loader = new ConfigLoaderYaml(new YamlParser(), $input);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }

    /**
     * @I Create a Drupal module for better integration incl. UI
     *    type     : improvement
     *    priority : normal
     *    labels   : integrations
     *    notes    : UI could allow site managers to manage toggles and features
     */
    public static function drupal($varName, $varValue = null, $path = NULL)
    {
        // @I Throw a specialized exception when the config file is not found
        //    type     : improvement
        //    priority : normal
        //    labels   : error-handling
        if (!$path) {
            $path = 'web/sites/default/toggle.yml';
        }

        $input = realpath(__DIR__ . '/../../../../' . $path);
        $loader = new ConfigLoaderYaml(new YamlParser(), $input);
        $toggle = new ToggleConfig($loader, $varName, $varValue);
        return $toggle->on();
    }
}
