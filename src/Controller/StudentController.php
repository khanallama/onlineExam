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

    #[Route('/studentresult', name: 'studentresult')]
    public function studentresult(): Response
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

    #[Route('/fetchQuestionAnswer', name: 'fetchQuestionAnswer')]
    public function submitAnswer(ManagerRegistry $managerRegistry): Response
    {
        $answers[0] =0 ;
        $this->denyAccessUnlessGranted('ROLE_STUDENT');
        $question = $managerRegistry->getRepository(Question::class)->findAll(); // fetching all data from Question entity.
        foreach ($question as $questions){
            $questionId = $questions->getId(); // fetching questions Id.
            $answers[$questionId]= $managerRegistry->getRepository(Answer::class)->findBy(array('question'=>$questionId)); // fetching answers with respect to their questons id.
        }
        $userName = $this->getUser();
        return $this->render('student/fetchQuestionAnswer.html.twig',[
            'question' => $question,
            'answer' => $answers,
            'user'     => $userName,
        ]);

    }

    /**
     * @param ManagerRegistry $registry
     * @param Request $request
     * @return Response
     *
     * catching data from twig file.
     */

    #[Route('/answerSubmit', name: 'answerSubmit')]
    public function answerSubmit(ManagerRegistry $registry , Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STUDENT'); // deny access the page without login
        $question = $registry->getRepository(Question::class)->findAll(); // fetching all data from Question entity.
        $answer = $request->get('answer'); // caching value from twig form
        $totalQuestions=count($question); // calculating total number of question present in question entity.
        $totalQuestionsAttempt=count($answer); //  calculating total number of question attempt.
        $score=0;

        foreach ($answer as $answers){
            foreach ($question as $questions){
                $questionId = $questions->getCorrectAnswerId();
                if($questionId == $answers){
                    $score++;
                }
            }
        }

        $this->addFlash('success',"Out of ". $totalQuestions."  you have attempted ".$totalQuestionsAttempt." you have scored ".$score);
        return $this->redirectToRoute('studentresult');
    }
}
