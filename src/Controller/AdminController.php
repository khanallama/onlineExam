<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/index.html.twig');
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */

    #[Route('/create-question',name: 'create_question_answer' , methods:'POST' )]
    public function createQuestionAnswer(ManagerRegistry $doctrine , Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //denying user access who is not login

        $managerRegistry = $doctrine->getManager();

        $question      = $request->get("question");
        $correctAnswer = $request->get("correctAnswer");
        $wrongAnswer1  = $request->get("wronganswer1");
        $wrongAnswer2  = $request->get("wronganswer2");
        $wrongAnswer3  = $request->get("wronganswer3");

        $questionsFetchDb = $doctrine->getRepository(Question::class)->findBy(array('question'=>$question));
        if ($questionsFetchDb) {
            $this->addFlash('danger',"Question Already Present");
            return $this->redirectToRoute('admin');
        }
        else{
            $questions = new Question();
            $questions->setQuestion($question);
            $managerRegistry->persist($questions);

            $answer = new Answer();
            $answer->setAnswers($correctAnswer);
            $answer->setQuestion($questions);
            $questions->setCorrectAnswer($answer);
            $managerRegistry->persist($answer);

            $wrongAnswers = [$wrongAnswer1, $wrongAnswer2, $wrongAnswer3];

            foreach ($wrongAnswers as $wrongAnswer) {
                $answer = new Answer();
                $answer->setAnswers($wrongAnswer);
                $answer->setQuestion($questions);
                $managerRegistry->persist($answer);
            }
            $managerRegistry->flush();

            $this->addFlash('success',"Question and Answers added");
            return $this->redirectToRoute('admin');
        }
    }
}