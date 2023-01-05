<?php

namespace Internal\Libraries;

use Exception;

/**
 * Validation library
 */
class Validation
{
	/**
	 * Valid rules
	 */
	public const Rules = ['NotNull', "IsNumber", "IsSet", "Bail"];

	/**
	 * @param {data} Object to validate
	 * @param {rules} Array of rules
	 */
	function __construct(protected object $data, protected array $rules)
	{
		foreach ($rules as $prop => $rules2) {
			foreach ($rules2 as $rule) {
				if (!in_array($rule, self::Rules)) {
					throw new Exception("Invalid rule $rule at $prop");
				}
			}
		}
	}

	/**
	 * Internal use only
	 *
	 * Validate single prop
	 */
	private function validateProp(string $prop, array $rules)
	{
		$errors = [];

		foreach ($rules as $rule) {
			switch ($rule) {
				case "Bail": {
						if (count($errors) >= 1) {
							return $errors;
						}

						break;
					}
				case "NotNull": {
						if ($this->data->$prop === null) {
							array_push($errors, "$prop must not be null");
						}

						break;
					}
				case "IsSet": {
						if (!isset($this->data->$prop)) {
							array_push($errors, "$prop must be set");
						}

						break;
					}
				case "IsNumber": {
						if (!(
							(is_float($this->data->$prop) ||
								gettype($this->data->$prop) === "double"
							) &&
							(is_numeric($this->data->$prop) ||
								gettype($this->data->$prop) === "integer"
							)
						)) {
							array_push($errors, "$prop must be number");
						}

						break;
					}
			}
		}

		return $errors;
	}

	/**
	 * Validate data
	 */
	public function validate(): array
	{
		$errors = [];

		foreach ($this->rules as $prop => $rules) {
			$errors = array_merge($errors, $this->validateProp($prop, $rules));
		}

		return [count($errors) === 0, $errors];
	}
}
