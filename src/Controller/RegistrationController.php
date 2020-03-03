<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\KonneckAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;



class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, KonneckAuthenticator $authenticator,\Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $rand = rand(1,2);
        $confirmationToken = md5(uniqid());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Registration successful check your email to activate account!');
            $user->setIsActive(false);
            $user->setUserStatus('offline');
            $user->setConfirmationToken($confirmationToken);
            $rand == 1 ? $user->setUserAvatar('/web/images/1.jpg') : $user->setUserAvatar('/web/images/2.jpg');

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->sendConfirmationEmailMessage($user, $mailer);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirect($this->generateUrl('app_register'));
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function sendConfirmationEmailMessage(User $user,\Swift_Mailer $mailer)
    {

        $confirmationToken = $user->getConfirmationToken();
        $username = $user->getName();

        $subject = 'Account activation';
        $email = $user->getEmail();

        $renderedTemplate = $this->render('registration/activation.html.twig', array(
            'username' => $username,
            'confirmationToken' => $confirmationToken
        ));

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom('saqibsaleem22@gmail.com')
            ->setTo($email)
            ->setBody($renderedTemplate, "text/html");

        $mailer->send($message);

    }

    /**
     * @Route("/user/activate/{token}", name="confirmAction")
     */
    public function confirmAction(Request $request, $token, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $userRepository->findBy(['confirmationToken'=>$token]);

        if (!$users)
        {
            throw $this->createNotFoundException('We couldn\'t find an account for that confirmation token');
        }
        $user = $users['0'];


        $user->setIsActive(true);
        $user->setConfirmationToken(null);
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('app_login'));
    }

    /**
     * @Route("/recover", name="recover")
     */
    public function recoverPassword(Request $request,UserRepository $userRepository,\Swift_Mailer $mailer) {

        if($request->getMethod()=="POST") {
            $em = $this->getDoctrine()->getManager();
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email'=>$email]);
            $confirmationToken = md5(uniqid());
            $user->setConfirmationToken($confirmationToken);
            $em->persist($user);
            $em->flush();

            $renderedTemplate = $this->render('registration/recoveryEmail.html.twig', array(
                'username' => $user->getName(),
                'confirmationToken' => $confirmationToken
            ));

            $message = (new \Swift_Message())
                ->setSubject('Recover Password Konneck')
                ->setFrom('saqibsaleem22@gmail.com')
                ->setTo($email)
                ->setBody($renderedTemplate, "text/html");

                $mailer->send($message);
                $this->addFlash('success', 'link sent, check your email');

        }
        return $this->render('security/recover.html.twig');
    }


    /**
     * @Route("/changePassword/{token}", name="changePassword")
     */
    public function changePassword(UserRepository $userRepository, $token) {

        return $this->render('registration/newpassword.html.twig', [
            'token'=> $token
        ]);
    }

    /**
     * @Route("/newPassword/{token}", name="newPassword")
     */
    public function newPassword(UserRepository $userRepository, $token,Request $request, UserPasswordEncoderInterface $passwordEncoder) {


        $newsPassword = $request->request->get('hola');
        $user = $userRepository->findOneBy(['confirmationToken'=>$token]);
        $user->setPassword($passwordEncoder->encodePassword($user, $newsPassword));
        $user->setConfirmationToken(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'password is changed go back to login');
        return $this->render('registration/newpassword.html.twig', [
            'token'=>$token
        ]);
    }
}
