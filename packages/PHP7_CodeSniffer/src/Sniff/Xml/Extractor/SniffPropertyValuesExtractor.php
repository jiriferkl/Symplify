<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor;

use SimpleXMLElement;

final class SniffPropertyValuesExtractor
{
    /**
     * @var bool[]
     */
    private $stringToBoolMap = [
        'true' => true,
        'TRUE' => true,
        'false' => false,
        'FALSE' => false
    ];

    public function extractFromRuleXmlElement(SimpleXMLElement $ruleXmlElement) : array
    {
        if (!isset($ruleXmlElement->properties)) {
            return [];
        }

        $propertyValues = [];
        foreach ($ruleXmlElement->properties->property as $propertyXmlElement) {
            $name = (string) $propertyXmlElement['name'];
            $value = $this->normalizeValue((string) $propertyXmlElement['value'], $propertyXmlElement);
            $propertyValues[$name] = $value;
        }

        return $propertyValues;
    }

    /**
     * @return mixed
     */
    private function normalizeValue(string $value, SimpleXMLElement $propertyXmlElement)
    {
        $value = trim($value);

        if (is_numeric($value)) {
            return (int) $value;
        }

        if ($this->isArrayValue($propertyXmlElement)) {
            return $this->normalizeArrayValue($value);
        }

        return $this->normalizeBoolValue($value);
    }

    private function isArrayValue(SimpleXMLElement $property) : bool
    {
        return isset($property['type']) === true && (string) $property['type'] === 'array';
    }

    /**
     * @return mixed
     */
    private function normalizeBoolValue(string $value)
    {
        if (isset($this->stringToBoolMap[$value])) {
            return $this->stringToBoolMap[$value];
        }

        return $value;
    }

    private function normalizeArrayValue(string $value) : array
    {
        $values = [];
        foreach (explode(',', $value) as $val) {
            $v = '';

            list($key, $v) = explode('=>', $val . '=>');
            if ($v !== '') {
                $values[$key] = $v;
            } else {
                $values[] = $key;
            }
        }

        return $values;
    }
}
