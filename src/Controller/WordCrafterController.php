<?php

namespace App\Controller;

use App\Entity\UserSubmission;
use App\Service\PuzzleGeneratorService;
use App\Service\WordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WordCrafterController extends AbstractController
{
    private $entityManager;
    private $puzzleGenerator;
    private $wordService;

    public function __construct(EntityManagerInterface $entityManager, PuzzleGeneratorService $puzzleGenerator, WordService $wordService)
    {
        $this->entityManager = $entityManager;
        $this->puzzleGenerator = $puzzleGenerator;
        $this->wordService = $wordService;
    }

    public function homepage(): Response
    {
        $highscores = [];
        try {
            
            $highscores = $this->wordService->getGameHighscores();
            $getMostRepeatedWords = $this->wordService->getMostRepeatedWords();
        } catch (\RuntimeException $e) {
            // Catch the specific exception thrown by the service
            $this->addFlash('error', 'The game is currently unavailable');
        }
        return $this->render('wordCrafter/homepage.html.twig', ['highscores' => $highscores,'mostRepeatedWords'=>$getMostRepeatedWords]);
    }

    public function startGame(Request $request): Response
    {
        $playerName = $request->request->get('player_name');
        if (empty($playerName)) {
            $this->addFlash('error', 'Player name is required.');
            return $this->redirectToRoute('homepage');
        }

        try {
            $puzzleString = $this->puzzleGenerator->generate();

            $submission = new UserSubmission();
            $submission->setUsername($playerName);
            $submission->setPuzzleWord($puzzleString);
            $submission->setRemainingLetters($puzzleString);

            $this->entityManager->persist($submission);
            $this->entityManager->flush();

            return $this->redirectToRoute('play_game', ['id' => $submission->getId()]);
        } catch (\RuntimeException $e) {
            // Handle exceptions thrown by the service
            $this->addFlash('error', 'Cannot generate puzzle: ' . $e->getMessage());
            return $this->redirectToRoute('homepage');
        } catch (\Throwable $e) {
            // Catch-all for any other error
            $this->addFlash('error', 'Unexpected error occurred. Please try again later.');
            return $this->redirectToRoute('homepage');
        }
    }

    public function playGame(UserSubmission $submission): Response
    {
        return $this->render('wordCrafter/game.html.twig', ['session' => $submission,]);
    }

    public function submitWord(Request $request, UserSubmission $submission): Response
    {
        try {
            $submittedWord = $request->request->get('word');
            // Validation logic
            if (empty($submittedWord) || !is_string($submittedWord)) {
                $this->addFlash('error', 'Please submit a valid word.');
            } elseif ($this->wordService->checkAlreadyCreatedWord($submittedWord, $submission->getSubmittedWords())) {
                $this->addFlash('error', 'This word has been already created,Try another one');
            } elseif (!$this->wordService->canFormWord($submittedWord, $submission->getRemainingLetters())) {
                $this->addFlash('error', 'Not sufficient letters remaining for this word.');
            } elseif (!$this->wordService->isValidEnglishWord($submittedWord)) {
                $this->addFlash('error', 'Not a valid English word.');
            } else {
                // Update the session
                $wordLetters = str_split(strtoupper($submittedWord));
                $remaining = str_split($submission->getRemainingLetters());
                foreach ($wordLetters as $letter) {
                    $pos = array_search($letter, $remaining);
                    if ($pos !== false) {
                        unset($remaining[$pos]);
                    }
                }

                $submission->setRemainingLetters(implode('', $remaining));
                $submission->setScore($submission->getScore() + strlen(trim($submittedWord)));
                $wordsUsed = $submission->getSubmittedWords();
                $wordsUsed[] = $submittedWord;
                $submission->setSubmittedWords($wordsUsed);
                $this->entityManager->flush();
                $this->addFlash('success', 'Congrats, that is a valid word');
            }
        } catch (\RuntimeException $e) {
            // Handle exceptions thrown by the service
            $this->addFlash('error', 'Cannot continue word crafter as : ' . $e->getMessage());
            return $this->redirectToRoute('play_game', ['id' => $submission->getId()]);
        } catch (\Throwable $e) {
            // Catch-all for any other error
            $this->addFlash('error', 'Unexpected error occurred. Please try again later.');
            return $this->redirectToRoute('play_game', ['id' => $submission->getId()]);
        }

        return $this->redirectToRoute('play_game', ['id' => $submission->getId()]);
    }
}
