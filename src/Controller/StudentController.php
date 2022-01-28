<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{

    #[Route('/test-result', name: 'test_result')]
    public function testResult(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STUDENT');
        return $this->render('student/index.html.twig');
    }

    /**
     * @param ManagerRegistry $managerRegistry
     * @return Response
     *
     * fetching questions and it's related answer from database and render all the data to twig file.
     */

    #[Route('/mcq-test', name: 'mcq_test')]
    public function submitAnswer(ManagerRegistry $managerRegistry): Response
    {
        $answers = [];
        $this->denyAccessUnlessGranted('ROLE_STUDENT');
        $questions = $managerRegistry->getRepository(Question::class)->findAll(); // fetching all data from Question entity.
        foreach ($questions as $question){
            $questionId = $question->getId();
            $answers[$questionId] = $managerRegistry->getRepository(Answer::class)->findBy(array('question'=>$questionId)); // fetching answers with respect to their questons id.
        }

        return $this->render('student/fetchQuestionAnswer.html.twig',[
            'questions' => $questions,
            'answers' => $answers,
        ]);

    }

    /**
     * @param ManagerRegistry $registry
     * @param Request $request
     * @return Response
     *
     * catching data from twig file.
     */

    #[Route('/test-submit', name: 'test_submit')]
    public function testSubmit(ManagerRegistry $registry , Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STUDENT');
        $questions = $registry->getRepository(Question::class)->findAll();
        $chosenAnswers = $request->get('answer');
        $totalQuestions = $request->get('total_questions');

        $totalQuestionsAttempt = count($chosenAnswers);
        $score = 0;

        foreach ($chosenAnswers as $answer){
            foreach ($questions as $question){
                $correctAnswerId = $question->getCorrectAnswer()->getId();
                if($correctAnswerId == $answer){
                    $score++;
                }
            }
        }

        $this->addFlash('success',"Out of ". $totalQuestions."  you have attempted ".$totalQuestionsAttempt." you have scored ".$score);
        return $this->redirectToRoute('test_result');
    }

}

