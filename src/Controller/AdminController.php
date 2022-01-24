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

    #[Route('/create',name: 'createQuestionAnswer' , methods:'POST' )]
    public function createQuestionAnswer(ManagerRegistry $doctrine , Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //denying user access who is not login

        $managerRegistry = $doctrine->getManager();

        $question      = $request->get("question");               // geting question from form and initilize it into variable
        $correctAnswer = $request->get("correctAnswer");         // getting correct answer from form
        $wrongAnswer1  = $request->get("wronganswer1");         // getting first wrong answer from form
        $wrongAnswer2  = $request->get("wronganswer2");        // getting second wrong answer from form
        $wrongAnswer3  = $request->get("wronganswer3");       // getting third wrong answer from form

        $correctAnswerId = date('His').rand(0,100);         // creating id so that we can pass it itno correct answer's id in answer entity and also pass in question entity to match tha correct answer with question

        $questionsFetch = $doctrine->getRepository(Question::class)->findBy(array('question'=>$question));  // feching question entity to check given question is exist or not

        if ($questionsFetch) {
            $this->addFlash('danger',"Question Already Present");  // if question exist , it will return  message
            return $this->redirectToRoute('admin');
        }
        else{
            $questions = new Question();                             // Creating object of Question entity
            $questions->setQuestion($question);                     // Setting question to the question entity.
            $questions->setCorrectAnswerId($correctAnswerId);      // Setting correct answer's id to the question in question entity.
            $managerRegistry->persist($questions);                //  inserting question with it's correct answer id into the database question entity.

            $answers = [$correctAnswerId => $correctAnswer, rand(0, 20) => $wrongAnswer1, rand(0, 101) => $wrongAnswer2, rand(0, 102) => $wrongAnswer3];   // Assigning corect answewr id to correct answer and random id to other answer which is wrong answer

            foreach ($answers as $answersId => $answerValue) {
                $answer = new Answer();               // Creating object of Answer entity
                $answer->setAnswerId($answersId);    // Setting  correct Answer id to the answer entity with respect to their answer.
                $answer->setAnswers($answerValue);    // setting answer value
                $answer->setQuestion($questions);     // setting question object to the answer object ,to set the question Id  with respect to their answers
                $managerRegistry->persist($answer);   // inserting answers with question id and answer id into the database answer entity
            }
            $managerRegistry->flush();               // Synchronizing all data to database.

            $this->addFlash('success',"Question and Answers added");
            return $this->render('admin/index.html.twig');
        }
    }
}
