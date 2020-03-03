<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
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
    public function loadDetailsOfUser(User $user, MessageRepository $messageRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUserId = $this->getUser()->getId();
        $otherUserId = $user->getId();
        $sentMessages = $messageRepository->findBy(['senderId'=>$currentUserId, 'receiverId'=>$otherUserId]);
        $receivedMessages = $messageRepository->findBy(['senderId'=>$otherUserId, 'receiverId'=>$currentUserId]);
        foreach($receivedMessages as $received) {
            $received->setMsgStatus('read');
            $em->persist($received);
            $em->flush();
        }


        $allMessages = Array();
        foreach($sentMessages as $sent) {
            array_push($allMessages, $sent);
        }
        foreach($receivedMessages as $received) {
            array_push($allMessages, $received);
        }

        usort($allMessages,function($first,$second){
            return $first->getId() > $second->getId();
        });



        return $this->json([
            'user' => $user,
            'allMessages' => $allMessages,
        ]);
    }

    /**
     * @Route("/sendMessage/{id}", name="sendMessage")
     */
    public function sendMessage(Request $request, User $user,FileUploader $fileUploader) {
        $current_user = $this->getUser();
        $file = $request->files->get('fileToUpload');

        $message = $request->request->get('msg-content');

        $newMsg = new Message();
        $newMessage = new Message();

        $newMessage->setMsgContent($message);
        $newMessage->setReceiverId($user->getId());
        $newMessage->setMsgDate(new \DateTime('now'));
        $newMessage->setDateTime(new \DateTime('now'));
        $newMessage->setSenderId($current_user->getId());

        $newMessage->setMsgStatus('unread');
        if(!$file){
            $newMessage->setMsgType('text');
        } else {

            $fileName = $fileUploader->uploadFile($file);
            $newMessage->setAttachments($fileName);
            $newMessage->setMsgType('file');

        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($newMessage);

        $em->flush();

        return $this->json([]);

    }
    /**
     * @Route("/showProfileOther/{id}", name="showProfileOther")
     */
    public function showProfileOther(User $user) {
        return $this->render('home/profileOther.html.twig', [
            'userOther' => $user
        ]);
    }

    /**
     * @Route("/newMessages", name="newMessages")
     */
    public function newMessages(MessageRepository $messageRepository) {

        $id = $this->getUser();
        $id = $id->getId();
        $messages = $messageRepository->findBy(['receiverId'=>$id, 'msgStatus'=>'unread']);
        return $this->json([
            'total' => $messages
        ]);
    }

    /**
     * @Route("/onlineStatus", name="onlinestatus")
     */
    public function onlineStatusCheck(UserRepository $userRepository) {
        $all = $userRepository->findAll();

        return $this->json(
        ['all' => $all]
        );
    }


}
