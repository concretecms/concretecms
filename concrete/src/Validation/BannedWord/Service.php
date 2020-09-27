<?php
namespace Concrete\Core\Validation\BannedWord;

class Service
{
    const CASE_FIRST_LOWER = 0;
    const CASE_FIRST_UPPER = 1;
    const CASE_HAS_LOWER = 2;
    const CASE_HAS_UPPER = 4;
    const CASE_HAS_NONALPH = 8;
    const CASE_MIXED = 6;

    const TRUNCATE_CHARS = '';
    const TRUNCATE_WORDS = '\s+';
    const TRUNCATE_SENTS = '[!.?]\s+';
    const TRUNCATE_PARS = '\n{2,}';

    public $bannedWords;

    public function getCSV_simple($file)
    {
        return false;
    }

    public function loadBannedWords()
    {
        if ($this->bannedWords) {
            return;
        }
        $bw = new BannedWordList();
        $bannedWords = $bw->get();
        $this->bannedWords = array();
        foreach ($bannedWords as $word) {
            $this->bannedWords[] = $word->getWord();
        }
    }

    public function wordCase($word)
    {
        $lower = "abcdefghijklmnopqrstuvwxyz";
        $UPPER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        $case = 0;
        while ($c = $word[$i]) {
            if (strpos($lower, $c) !== false) {
                if ($i == 0) {
                    $case |= self::CASE_FIRST_LOWER;
                } else {
                    $case |= self::CASE_HAS_LOWER;
                }
            } elseif (strpos($UPPER, $c) !== false) {
                if ($i == 0) {
                    $case |= self::CASE_FIRST_UPPER;
                } else {
                    $case |= self::CASE_HAS_UPPER;
                }
            } else {
                $case |= self::CASE_HAS_NONALPH;
            }
            ++$i;
        }

        return $case;
    }

    public function forceCase($case, &$word)
    {
        $word = strtolower($word);
        if ($case & self::CASE_FIRST_UPPER) {
            $word = ucfirst($word);
        }
        $c = $case & self::CASE_MIXED;
        $i = 1;
        while ($word[$i]) {
            if ($c == self::CASE_HAS_UPPER ||
            ($c == self::CASE_MIXED && !round(mt_rand(0, 2)))
               ) {
                $word[$i] = strtoupper($word[$i]);
            }
            ++$i;
        }
    }

    public function isBannedWord(&$word)
    {
        $case = self::wordCase($word);
        $nword = strtolower($word);
        $this->loadBannedWords();
        if (in_array($nword, $this->bannedWords)) {
            return true;
        }

        return false;
    }

    /**
     * @param string[] $bannedWords
     */
    public function setBannedWords($bannedWords)
    {
        $this->bannedWords = $bannedWords;
                }

    /**
     * @return string[]
     */
    public function getBannedWords()
    {
        if (!is_array($this->bannedWords) || count($this->bannedWords) === 0) {
            $this->loadBannedWords();
        }
        return $this->bannedWords;
            }

    /**
     * @param string $string String to check it has banned words. From version 9, it won't be passed by reference.
     * @return int Return 1 if string has banned words, otherwise 0. From version 9, it will return boolean.
     */
    public function hasBannedWords(&$string)
    {
        $out = 0;
        $bannedWords = $this->getBannedWords();
        foreach ($bannedWords as $bannedWord) {
            if (mb_stripos($string, $bannedWord) !== false) {
                $out = 1;
                break;
        }
        }

        return $out;
    }

    public function hasBannedPart($string)
    {
        $this->loadBannedWords();
        $string = strtolower($string);
        foreach ($this->bannedWords as $bw) {
            if (strpos($string, $bw) !== false) {
                return true;
            }
        }

        return false;
    }

    public function truncate($string, $num, $which = self::TRUNCATE_CHARS, $ellipsis = "&#8230;")
    {
        $parts = preg_split("/($which)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        $i = 0;
        $out = "";
        while (count($parts) && ++$i < $num) {
            $out .= array_shift($parts).array_shift($parts);
        }
        if (count($parts)) {
            $out = trim($out).$ellipsis;
        }

        return $out;
    }

    public function getBannedKeys($inputArray)
    {
        $error_keys = array();
        if (is_array($inputArray) && count($inputArray)) {
            foreach (array_keys($inputArray) as $k) {
                if (is_string($inputArray[$k]) && $this->hasBannedWords($inputArray[$k])) {
                    $error_keys[] = $k;
                } elseif (is_array($inputArray[$k]) && count($inputArray[$k])) {
                    foreach ($inputArray[$k] as $v) {
                        if ($this->hasBannedWords($v)) {
                            $error_keys[] = $k;
                            break;
                        }
                    }
                }
            }
        }

        return $error_keys;
    }
}
