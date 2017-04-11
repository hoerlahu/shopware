<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\CartBundle\Domain\Validator;

use Shopware\Components\ReflectionHelper;

class RuleBuilder
{
    /**
     * @var ReflectionHelper
     */
    private $reflector;

    public function __construct()
    {
        $this->reflector = new ReflectionHelper();
    }

    /**
     * @param array $rule
     *
     * @return mixed
     */
    public function build($rule)
    {
        if (!is_array($rule)) {
            return $rule;
        }

        if (!array_key_exists('_class', $rule)) {
            if (array_key_exists(0, $rule) && array_key_exists('_class', $rule[0])) {
                return array_map([$this, 'build'], $rule);
            }

            return $rule;
        }

        $class = $rule['_class'];
        unset($rule['_class']);

        $params = array_map(function ($param) {
            if (is_array($param)) {
                return array_map([$this, 'build'], $param);
            }

            return $this->build($param);
        }, $rule);

        return $this->reflector->createInstanceFromNamedArguments($class, $params);
    }
}