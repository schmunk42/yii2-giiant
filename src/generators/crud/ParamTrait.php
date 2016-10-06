<?php

namespace schmunk42\giiant\generators\crud;

/**
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */
trait ParamTrait
{
    /**
     * {@inheritdoc}
     */
    public function generateActionParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            return '$'.$pks[0]; // fix for non-id columns
        } else {
            return '$'.implode(', $', $pks);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param '.(substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string').' $'.$pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param '.$table->columns[$pks[0]]->phpType.' $'.$pks[0]];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param '.$table->columns[$pk]->phpType.' $'.$pk;
            }

            return $params;
        }
    }

    /**
     * Generates URL parameters.
     *
     * @return string
     */
    public function generateUrlParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'{$pks[0]}' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }
}
