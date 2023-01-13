<?php

namespace App\Http\Requests;

use App\Providers\EditableEntitiesServiceProvider;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SafeFormRequest extends FormRequest
{
    /**
     * Create the default validator instance.
     *
     * @param ValidationFactory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function createDefaultValidator(ValidationFactory $factory)
    {
        return $factory->make(
            $this->validationData(), $this->container->call([$this, 'overallRules']),
            $this->overallMessages(), $this->attributes()
        );
    }

    /**
     * Рекурсивная очистка от небезопасных полей запроса для данных, валидирующихся через nestedRules
     *
     * @param array $data
     * @param array $rules
     * @return array
     */
    private function stripUnsafeAttributes($data, $rules)
    {
        $passAttributes = array_keys($rules);
        $requestAttributes = array_keys($data);
        foreach ($rules as $attribute => $rule) {
            if (is_array($rule) && Arr::has($data, $attribute)) {
                if (Arr::isAssoc($data[$attribute])) {
                    $data[$attribute] = $this->stripUnsafeAttributes($data[$attribute], $rule);
                } else {
                    foreach ($data[$attribute] as $idx => $item) {
                        $data[$attribute][$idx] = $this->stripUnsafeAttributes($item, $rule);
                    }
                }
            }
        }
        $unsafeAttributes = array_diff($requestAttributes, $passAttributes);
        foreach ($unsafeAttributes as $unsafeAttribute) {
            unset($data[$unsafeAttribute]);
        }
        return $data;
    }

    final public function removeUnsafeAttributes()
    {
        $passAttributes = array_keys($this->overallRules());
        if (method_exists($this, 'safeAttributes')) {
            $passAttributes = array_merge($passAttributes, $this->safeAttributes());
        }
        if (method_exists($this, 'nestedRules')) {
            $passAttributes = array_merge($passAttributes, array_keys($this->nestedRules()));

            foreach ($this->nestedRules() as $nestedAttribute => $nestedAttributeRules) {
                $nestedData = $this->json()->get($nestedAttribute);
                if (!empty($nestedData)) {
                    if (Arr::isAssoc($nestedData)) {
                        $nestedData = $this->stripUnsafeAttributes($nestedData, $nestedAttributeRules);
                    } else {
                        foreach ($nestedData as $idx => $nestedDataItem) {
                            $nestedData[$idx] = $this->stripUnsafeAttributes($nestedDataItem, $nestedAttributeRules);
                        }
                    }
                    $this->json()->set($nestedAttribute, $nestedData);
                }
            }
        }

        $requestAttributes = array_keys($this->all());

        $unsafeAttributes = array_diff($requestAttributes, $passAttributes);
        foreach ($unsafeAttributes as $unsafeAttribute) {
            $this->json()->remove($unsafeAttribute);
        }
    }

    protected function defaultRules()
    {
        return [
            'moderation_status' => 'in:' . implode(',', array_keys(EditableEntitiesServiceProvider::$possibleStatuses)),
        ];
    }

    protected function defaultMessages()
    {
        return [
            'moderation_status.in' => 'Incorrect moderation status',
        ];
    }

    public function overallRules()
    {
        if (!method_exists($this, 'rules')) {
            throw new \Exception("SafeFormRequest must contain rules() method");
        }
        if (empty($this->rules())) {
            throw new \Exception("SafeFormRequest rules() method does not define any rules");
        }
        return array_merge($this->rules(), $this->defaultRules());
    }

    public function overallMessages()
    {
        if (!method_exists($this, 'messages')) {
            return $this->defaultMessages();
        }
        return array_merge($this->messages(), $this->defaultMessages());
    }
}
