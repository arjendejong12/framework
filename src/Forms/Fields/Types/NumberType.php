<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Fields\Contracts\CanHandlePageSettings;
use Themosis\Forms\Transformers\NumberToLocalizedStringTransformer;

class NumberType extends BaseType implements CanHandleMetabox, CanHandlePageSettings
{
    /**
     * NumberType field view.
     *
     * @var string
     */
    protected $view = 'types.number';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'number';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.number';

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->allowedOptions = $this->setAllowedOptions();
        $this->defaultOptions = $this->setDefaultOptions();
    }

    /**
     * Set field specific allowed options.
     *
     * @return array
     */
    protected function setAllowedOptions(): array
    {
        return array_merge($this->allowedOptions, [
            'precision'
        ]);
    }

    /**
     * Set field options default values.
     *
     * @return array
     */
    protected function setDefaultOptions(): array
    {
        return array_merge($this->defaultOptions, [
            'precision' => 0
        ]);
    }

    /**
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new NumberToLocalizedStringTransformer($this->getLocale(), $this));

        return parent::parseOptions($options);
    }

    /**
     * Handle metabox post meta registration.
     *
     * @param mixed $value
     * @param int   $post_id
     */
    public function metaboxSave($value, int $post_id)
    {
        $this->setValue($value);

        $previous = get_post_meta($post_id, $this->getName(), true);

        if (is_null($this->getValue()) || empty($this->getValue())) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous)) {
            add_post_meta($post_id, $this->getName(), $this->getRawValue(), true);
        } else {
            update_post_meta($post_id, $this->getName(), $this->getRawValue(), $previous);
        }
    }

    /**
     * Initialize metabox post meta value.
     *
     * @param int $post_id
     */
    public function metaboxGet(int $post_id)
    {
        $value = get_post_meta($post_id, $this->getName(), true);

        if (! empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Save the field setting value.
     *
     * @param mixed  $value
     * @param string $name
     */
    public function settingSave($value, string $name)
    {
        //
    }

    /**
     * Return the field setting value.
     *
     * @return mixed|void
     */
    public function settingGet()
    {
        //
    }
}
