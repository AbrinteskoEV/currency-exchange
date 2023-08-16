<?php

declare(strict_types = 1);

namespace Application\Http\Request;

use Application\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Laravel\Lumen\Application;

/**
 * @info Bootstrap this with afterResolving BaseRequest ... { $request->performValidation(); }
 */
class BaseRequest extends Request implements RequestRulesInterface
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $data = parent::get('data');

        if (is_array($data)) {
            return $data[$key] ?? $default;
        }

        return parent::get($key, $default);
    }

    /**
     * @param string|null $key
     * @param null $default
     *
     * @return mixed
     */
    public function post($key = null, $default = null): mixed
    {
        $data = parent::post('data');

        if (is_array($data)) {
            return $key === null ? $data : $data[$key] ?? $default;
        }

        return parent::post($key, $default);
    }

    /**
     * @param null $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function input($key = null, $default = null): mixed
    {
        $dataExist = parent::input('data');
        $inputSource = $this->getInputSource();
        if($inputSource && $dataExist){
            $target = $inputSource->all('data') + $this->query->all('data');
        } else {
            $target = $inputSource->all() + $this->query->all();
        }

        return data_get(
            $target,
            $key,
            $default
        );
    }

    /**
     * @param Application $container
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function performValidation(Application $container): void
    {
        $factory = $container->make(Factory::class);

        /** @var Validator $validator */
        $validator = $factory->make($this->all(), $this->rules());

        if ($validator->fails()) {
            $messageBag = $validator->getMessageBag();
            $customMessages = $this->getCustomMessages($messageBag);

            throw new ValidationException('Validation failed', $customMessages);
        }
    }

    /**
     * @param MessageBag $messageBag
     *
     * @return array
     */
    private function getCustomMessages(MessageBag $messageBag): array
    {
        $messages = $messageBag->messages();
        $customMessages = $this->messages();

        foreach($customMessages as $attribute => $customMessage) {
            if (isset($messages[$attribute])) {
                $messages[$attribute] = [$customMessage];
            }
        }

        return $messages;
    }
}
