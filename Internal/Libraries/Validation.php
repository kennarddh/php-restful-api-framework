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
	private function validateProp(mixed $data, string $prop, array $rules)
	{
		$errors = [];

		foreach ($rules as $rule) {
			switch ($rule) {
				case "Bail": {
						if (count($errors) >= 1) {
							return [$errors, true];
						}

						break;
					}
				case "NotNull": {
						if ($data === null) {
							array_push($errors, "$prop must not be null");
						}

						break;
					}
				case "IsSet": {
						if (!isset($data)) {
							array_push($errors, "$prop must be set");
						}

						break;
					}
				case "IsNumber": {
						if (!(
							(is_float($data) &&
								gettype($data) === "double"
							) ||
							(is_numeric($data) &&
								gettype($data) === "integer"
							)
						)) {
							array_push($errors, "$prop must be number");
						}

						break;
					}
			}
		}

		return [$errors, false];
	}

	/**
	 * Validate data
	 */
	public function validate(): array
	{
		$errors = [];


		foreach ($this->rules as $prop => $rules) {
			$data = $this->data;

			if (str_contains($prop, '.')) {
				$explodedProp = explode('.', $prop);

				foreach ($explodedProp as $singleProp) {
					if (!isset($data->$singleProp)) {
						array_push($errors, "$prop must be set");
						break 2;
					}

					$data = $data->$singleProp;
				}
			} else {
				if (!isset($this->data->$prop)) {
					array_push($errors, "$prop must be set");
					break;
				}

				$data = $this->data->$prop;
			}

			[$newErrors, $isBailed] = $this->validateProp($data, $prop, $rules);

			$errors = array_merge($errors, $newErrors);

			if ($isBailed) {
				break;
			}
		}

		return [count($errors) === 0, $errors];
	}
}
