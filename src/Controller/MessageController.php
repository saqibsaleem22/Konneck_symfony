<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\FileUploader;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class MessageController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $current_user = $this->getUser();
        $allUsers = $userRepository->findAll();
        $others = Array();

        foreach ($allUsers as $use) {
            if($use->getId() != $current_user->getId()) {
                array_push($others, $use);
            }
        }

        return $this->render('home/home.html.twig', [
            'others' => $others
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function showProfile() {

        return $this->render('home/profile.html.twig');
    }

    /**
     * @Route("/modifyProfile/{id}", name="modifyProfile")
     */
    public function editProfile(Request $request, User $user, FileUploader $fileUploader) {
        $userName = $request->request->get('u_name');
        $userCity = $request->request->get('u_city');
        $userGender = $request->request->get('u_gender');
        $file = $request->files->get('u_avatar');
        $userPassword = $request->get('u_pass');
        $newPassword = $request->get('new_pass');
        if($userPassword == $user->getPassword() && $newPassword != ''){
            $user->setPassword($newPassword);

        }
        if($userName != ''){
            $user->setUserName($userName);
        }
        if($userCity != ''){
            $user->setUserCity($userCity);
        }
        if($userGender != ''){
            $user->setUserGender($userGender);
        }
        /** @var UploadedFile $file**/
        if($file) {
            $filename = $fileUploader->uploadFile($file);
            $user->setUserAvatar('/web/images/'.$filename);
        }
        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profile updated successfully');
            return $this->redirect($this->generateUrl('profile'));
        } catch (\Exception $exception) {
            $this->addFlash('error', 'Error updating profile username should be unique!');
            return $this->redirect($this->generateUrl('profile'));
        }
    }

    /**
     * @Route("/loadDetails/{id}", name="loadDetails")
     */
    public function loadDetailsOfUser(User $user)
    {

        return $this->json([
            'user' => $user
        ]);
    }

    /**
     * @Route("/sendMessage/{id}", name="sendMessage")
     */
    public function sendMessage(Request $request, User $user) {
        $current_user = $this->getUser();
        $file = $request->files->get('fileToUpload');

        $message = $request->request->get('msg-content');

        $newMsg = new Message();
        $newMessage = new Message();

        $newMessage->setMsgContent($message);
        $newMessage->setReceiverId($user->getId());
        $newMessage->setMsgDate(new \DateTime('now'));
        $newMessage->setSenderId($current_user->getId());

        $newMessage->setMsgStatus('unread');
        if(!$file){
            $newMessage->setMsgType('text');
        } else {
            $newMessage->setMsgType('file');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($newMessage);

        $em->flush();

        return $this->json();

    }
}
