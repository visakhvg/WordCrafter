<?php

namespace App\Service;

class PuzzleGeneratorService
{
    private $wordService;

    public function __construct(WordService $wordService)
    {
        $this->wordService = $wordService;
    }

    public function generate(){
        //dictionary.txt is added to the assets
        $dictionaryPath = __DIR__ . '/../../assets/dictionary.txt';
        if (!file_exists($dictionaryPath)) {
            throw new \RuntimeException('Dictionary file not found at ' . $dictionaryPath);
        }
        $dictionary = file($dictionaryPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($dictionary)) {
            throw new \RuntimeException('Dictionary file is empty.');
        }
        // Shuffle dictionary and  picks random number of words between $minWords and $maxWords
        shuffle($dictionary);
        $minWords=3;
        $maxWords = 5;
        $numWords = rand($minWords, $maxWords);

        //$numwords is slected from dictionary
        $selectedWords = array_slice($dictionary, 0, $numWords);

        // Combine and shuffle letters
        $combined = implode('', $selectedWords);
        $shuffled = str_shuffle($combined);

        return strtoupper($shuffled);
    }
}
