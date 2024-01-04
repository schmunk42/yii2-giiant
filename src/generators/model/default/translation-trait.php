<?php
/**
 * @var string $ns
 */

echo "<?php" . PHP_EOL
?>

namespace <?php echo $ns ?>;

use dosamigos\translateable\TranslateableBehavior;

trait TranslationAttributeRules
{
    /**
     * Import rules from translation model for translation attributes
     *
     * @return array
     */
    public function importTranslationAttributeRules(): array
    {
        $rules = [];
        foreach ($this->getBehaviors() as $behavior) {
            if ($behavior instanceof TranslateableBehavior) {
                $translationModelClass = $this->getRelation($behavior->relation)->modelClass;
                $importRules = (new $translationModelClass)->rules();
                foreach ($importRules as $rule) {
                    foreach ((array)$rule[0] as $rule_key => $attribute) {
                        if (!in_array($attribute, $behavior->translationAttributes, true)) {
                            unset($rule[0][$rule_key]);
                        }
                    }
                    if (!empty($rule[0])) {
                        $rules[] = $rule;
                    }
                }
            }
        }
        return $rules;
    }
}
