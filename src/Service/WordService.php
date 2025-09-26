<?php

namespace App\Service;

use App\Entity\UserSubmission;
use Doctrine\ORM\EntityManagerInterface;

class WordService
{
    private $dictionary;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isValidEnglishWord(string $word)
    {
        // Simple check against the in-memory dictionary added here to overcome execption error
        //dictionary.txt is added to the assets
        $dictionaryPath = __DIR__ . '/../../assets/dictionary.txt';
        if (!file_exists($dictionaryPath)) {
            throw new \RuntimeException('Dictionary file not found at ' . $dictionaryPath);
        }
        $this->dictionary = file_get_contents($dictionaryPath);
        $this->dictionary = explode("\n", $this->dictionary);
        $this->dictionary = array_map('trim', $this->dictionary);
        return in_array(strtolower($word), $this->dictionary);
    }

    public function checkAlreadyCreatedWord(string $word, $submittedWords)
    {

        if (!empty($submittedWords)) {
            if (in_array($word, $submittedWords)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function canFormWord(string $word, string $availableLetters)
    {
        $wordLetters = str_split(strtolower($word));
        $availableLettersArr = str_split(strtolower($availableLetters));

        foreach ($wordLetters as $letter) {
            $pos = array_search($letter, $availableLettersArr);
            if ($pos === false) {
                return false; // Letter not found
            }
            // Remove the used letter from the available letters array
            unset($availableLettersArr[$pos]);
        }

        return true;
    }

    public function getGameHighScores()
    {
        return $this->entityManager->getRepository(UserSubmission::class)->findBy([], ['score' => 'DESC', 'username' => 'ASC'], 10);
    }

    public function getMostRepeatedWords(): array
    {

        $submissions = $this->entityManager->getRepository(Usersubmission::class)->findAll();
        $wordCounts = [];
        foreach ($submissions as $submission) {
            $words = $submission->getSubmittedWords();

            foreach ($words as $word) {
                $word = strtolower(trim($word));
                if (!isset($wordCounts[$word])) {
                    $wordCounts[$word] = 0;
                }
                $wordCounts[$word]++;
            }
        }
        arsort($wordCounts);
        // dump($wordCounts);die;
        return array_slice($wordCounts, 0, 10, true);
    }
}
