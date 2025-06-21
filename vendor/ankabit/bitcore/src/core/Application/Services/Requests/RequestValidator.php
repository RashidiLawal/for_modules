<?php

declare(strict_types=1);

namespace BitCore\Application\Services\Requests;

use BitCore\Foundation\Validation\Factory as ValidationFactory;
use BitCore\Foundation\Validation\ValidationException;

abstract class RequestValidator
{
    protected static RequestInput $requestInput;
    protected static ValidationFactory $validator;

    public static function init(ValidationFactory $validator, RequestInput $requestInput)
    {
        self::$validator = $validator;
        self::$requestInput = $requestInput;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public static function validate(array|object $data)
    {
        $validation =  static::$validator->make($data, static::rules());

        if ($validation->fails()) {
            return $validation->errors();
        }

        return null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public static function data()
    {
        $method =  static::$requestInput->getRequest()->getMethod();
        $rules = static::rules();
        $data = [];
        if (strtolower($method) === 'get') {
            $data = static::$requestInput->get(array_keys($rules));
        } else {
            $data = static::$requestInput->post(array_keys($rules));

            // Add uploaded files
            $files = static::$requestInput->files();
            if ($files) {
                $data = array_merge($data, $files);
            }
        }

        return $data;
    }

    /**
     * Get the data that have been validated from the input.
     * @throws ValidationException
     */
    public static function validated()
    {
        $rules = static::rules();
        $data = static::data();
        $validator =  static::$validator->make($data, $rules);
        $validator->setException(ValidationException::class);
        return $validator->validated();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public static function authorize(): bool
    {
        return true;
    }

    abstract public static function rules(): array;
}
