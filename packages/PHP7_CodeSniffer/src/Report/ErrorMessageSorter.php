<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Report;

final class ErrorMessageSorter
{
    public function sortByFileAndLine(array $errorMessages) : array
    {
        ksort($errorMessages);

        foreach ($errorMessages as $file => $errorMessagesForFile) {
            if (count($errorMessagesForFile) <= 1) {
                continue;
            }

            usort($errorMessagesForFile, function ($first, $second) {
                return ($first['line'] > $second['line']);
            });

            $errorMessages[$file] = $errorMessagesForFile;
        }

        return $errorMessages;
    }
}
