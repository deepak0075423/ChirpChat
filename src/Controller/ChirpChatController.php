<?php
namespace App\Controller;
use App\Services\DisplayImg;
use Pusher\Pusher;

session_start();

use App\Entity\Comments;
use App\Entity\FriendList;
use App\Entity\FriendRequest;
use App\Entity\LikeDislike;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Login;
use App\Services\UserOperations;
use Symfony\Component\HttpFoundation\Request;

/**
 * The ChirpChat controller manages all the nessary things required for main
 * page like data loading, adding post, adding comments, showing comments etc.
 * 
 * @package ORM
 * @subpackage Doctrine
 * 
 * @author Deepak Pandey <deepak.pandey@innoraft.com>
 */
class ChirpChatController extends AbstractController
{
    /**
     * @var object
     *    Request object handles parameter from query parameter.
     */
    private $em;

    /**
     * @var object
     *    Instance of login Repository.
     */
    private $login;

    /**
     * @var object
     *    Instance of Posts Repository.
     */
    private $allPosts;

    /**
     * @var object
     *    Instance of Comments Repository.
     */
    private $comments;

    /**
     * @var object
     *    Instance of LikeDislike Repository.
     */
    private $likeDislike;

    /**
     * @var object
     *    Object of Posts Repository.
     */
    private $postDetails;

    /**
     * @var object
     *    Object of Comments Repository.
     */
    private $commentsDetails;

    /**
     * @var object
     *    Object of LikeDislike Repository.
     */
    private $likeDis;

    /**
     * @var object
     *   Object of UserOperation class which takes value from database and
     *   returns all values in the form of array.
     */
    private $user;

    private $friendReq;
    private $friendSend;
    private $friendList;

    /**
     * This constructor is used to initializing the object and also provides the
     * access to EntityManagerInterface
     * 
     * @param object $em
     *   Request object handles parameter from query parameter.
     * @param object $login
     *   Instance of login Repository.
     * @param object $allPosts
     *   Instance of Posts Repository.
     * @param object $comments
     *   Instance of Comments Repository.
     * @param object $likeDislike
     *   Instance of LikeDislike Repository.
     * @param object $postDetails
     *   Object of Posts Repository.
     * @param object $commentsDetails
     *   Object of Comments Repository.
     * @param object $likeDis
     *   Object of LikeDislike Repository.
     * @param object $user
     *   Object of UserOperation class which takes value from database and
     *   returns all values in the form of array.
     * 
     * @return void
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->login = $this->em->getRepository(login::class);
        $this->allPosts = $this->em->getRepository(Posts::class);
        $this->comments = $this->em->getRepository(Comments::class);
        $this->likeDislike = $this->em->getRepository(LikeDislike::class);
        $this->friendReq = $this->em->getRepository(FriendRequest::class);
        $this->friendList = $this->em->getRepository(FriendList::class);
        $this->friendSend = new FriendRequest();
        $this->postDetails = new Posts();
        $this->commentsDetails = new Comments();
        $this->likeDis = new LikeDislike();
        $this->user = new UserOperations();
    }

    /**
     * This routes opens the main page if user logged in and if not logged in 
     * it will open the login page.
     *
     * @param object $session
     *   Session object store session variable.
     *
     * @return Response
     *   Returns main or login page.
     */
    #[Route('/', name: 'main')]
    public function main(SessionInterface $session, Pusher $pusher)
    {
        $email = $session->get('email');
        $total = $this->friendReq->count(['requestStatus' => 0]);
        $pusher->trigger('ChirpChat-development', 'my-noti', $total);
        $email = $session->get('email');
        if (isset($email)) {
            return $this->render(view:'main/index.html.twig');
        }
        return $this->render(view:'login/login.html.twig');
    }

    /**
     * This routes returns the all the required data from login table.
     *
     * @param object $session
     *   Session object store session variable.
     *
     * @return Response
     *   Returns array of data from login table.
     */
    #[Route('/dataLoad', name: 'dataLoad')]
    public function dataLoad(SessionInterface $session) {
        $email = $session->get('email');
        $showDatas = $this->login->findOneBy(['email' => $email]);
        $dataArr = [];
        $dataArr[] = [
            'id' => $showDatas->getId(),
            'firstname' => $showDatas->getFirstName(),
            'lastname' => $showDatas->getLastName(),
            'img' => $showDatas->getImg(),
            'about' => $showDatas->getAbout(),
            'email' => $showDatas->getEmail(),
        ];
        return new JsonResponse(['dataArr' => $dataArr]);
    }

    /**
     * This routes returns the list of online and ofline user.
     *
     * @return Response
     *   Returns array of online and offlineuser data from login table.
     */
    
    #[Route('/onlineUser', name: 'onlineUser')]
    public function onlineUser(SessionInterface $session)
    {
        $emailId = $session->get('email');
        $userOnline = $this->login;
        $users = $userOnline->getOnlineUsers($emailId);

        $onlineUser = $this->user->getOnlineUser($users);
        return new JsonResponse(['userOnline' => $onlineUser]);
    }

    /**
     * This routes returns the current posts and post information.
     * 
     * @param object $session
     *   Session object store session variable.
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns array of current post and post information.
     */
    #[Route('/post', name: 'post')]
    public function post(Request $request , SessionInterface $session) {
        $emailId = $session->get('email');
        if ($request->isXmlHttpRequest()) {
            $post = $request->request->get('post');
            if ($post) {
                $this->postDetails->setPostDetails($post);
            
            $this->postDetails->setDetails($this->login->findOneBy(['email'=> $emailId]));
            $this->em->persist($this->postDetails);
            $this->em->flush();
            }
            $allPostsDetails = $this->allPosts->findAll();
    
            $currentPosts = $this->user->getAllPosts($emailId, $allPostsDetails);
            return new JsonResponse(['posts' => $currentPosts]);
        }
        return new JsonResponse('failed');
    }

    /**
     * This route Delete the post.
     * 
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a message that the post is deleted or not.
     */
    #[Route('/deletePost', name: 'deletePost')]
    public function deletePost(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('i');
            $deletedCommentsRec = $this->comments->findOneBy(['commentsDetails' => $id]);
            if ($deletedCommentsRec) {
                $this->em->remove($deletedCommentsRec);
            }
            $likedis = $this->likeDislike->findOneBy(['postDel' => $id]);
            if ($likedis) {
                $this->em->remove($likedis);
            }
            $deletedRec = $this->allPosts->findOneBy(['id' => $id]);
            $this->em->remove($deletedRec);
            $this->em->flush();
            return new Response('done');
        }
        return new Response('Failed');        
    }

    /**
     * This route Edit the post.
     * 
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a message that the post is edited or not.
     */
    #[Route('/editPost', name: 'editPost')]
    public function editPost(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $editVal = $request->request->get('afterEdit');
            $id = $request->request->get('i');
            $editRec = $this->allPosts;
            $editedRec = $editRec->findOneBy(['id' => $id]);
            $editedRec->setPostDetails($editVal);
           
            $this->em->flush();
            return new Response('done');
        }
        return new Response('Failed');
    }
    
    /**
     * This routes returns the current comment and comment information.
     * 
     * @param object $session
     *   Session object store session variable.
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns array of current comment and comment information.
     */
    #[Route('/addComment', name: 'addComment')]
    public function addComment(Request $request, SessionInterface $session) {
        if ($request->isXmlHttpRequest()) {
            $addcomments = $request->request->get('addComment');
            $id = $request->request->get('i');
            $emailId = $session->get('email');
            if ($addcomments) {
                $this->commentsDetails->setComments($addcomments);
            $this->commentsDetails->setCommentsDetails($this->allPosts->find($id));
            $this->commentsDetails->setLoginComments($this->login->findOneBy(['email' => $emailId]));
            $this->em->persist($this->commentsDetails);
            $this->em->flush();
            }
            $allCommentsD = $this->allPosts;
            $showAllCommentsDetails = $allCommentsD->find($id);

            $currentComments = $this->user->getComments($emailId, $showAllCommentsDetails);
            return new JsonResponse(['comm' => $currentComments]);
        }
        return new Response('Failed');
    }

    /**
     * This route Delete the comment.
     * 
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a message that the comment is deleted or not.
     */
    #[Route('/deleteComm', name: 'deleteComm')]
    public function deleteComm(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('i');
            $deletedCommentsRec = $this->comments->find($id);
            $this->em->remove($deletedCommentsRec);
            $this->em->flush();
            return new Response('done');
        }
        return new Response('Failed');
    }

    /**
     * This route Edit the comment.
     * 
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a message that the comment is edited or not.
     */
    #[Route('/editComm', name: 'editComm')]
    public function editComm(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $editVal = $request->request->get('afterEdit');
            $id = $request->request->get('i');
            $editedRec = $this->comments->findOneBy(['id' => $id]);
            $editedRec->setComments($editVal);
           
            $this->em->flush();
            return new Response('done');
        }
        return new Response('Failed');
    }

    /**
     * This route dislike the post.
     * 
     * @param object $session
     *   Session object store session variable.
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a count of dislike.
     */
    #[Route('/dislike', name: 'dislike')]
    public function dislike(Request $request, SessionInterface $session) {
        if ($request->isXmlHttpRequest()) {
            $dis = $request->request->get('dis');
            $id = $request->request->get('id');
            $emailId = $session->get('email');
            $editRec = $this->allPosts;
            $editedRec = $editRec->find($id);
            $editedRec->setThumsDown($editedRec->getThumsDown() + $dis);
            $thumpsDown = $editedRec->getThumsDown();
            $loginId = $this->login->findOneBy(['email' => $emailId])->getId();
            $likeDislikecheck = $this->likeDislike->findOneBy(['likeDislike' => $loginId, 'postDel' => $id]);
            if ($likeDislikecheck) {
                $likeDislikecheck->setThDown("blue");
                $likeDislikecheck->setThUp("black");
            }
            else {
                $this->likeDis->setThDown("blue");
                $this->likeDis->setThUp("black");
                $this->likeDis->setPostDel($this->allPosts->find($id));
                $this->likeDis->setLikeDislike($this->login->findOneBy(['email' => $emailId]));
                $this->em->persist($this->likeDis);
            }
            $this->em->flush();
            return new Response($thumpsDown);
        }
        return new Response('Failed');
    }

    /**
     * This route remove dislike from the post.
     * 
     * @param object $session
     *   Session object store session variable.
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a count of dislike after dislike.
     */
    #[Route('/dislikeRemove', name: 'dislikeRemove')]
    public function dislikeRemove(Request $request, SessionInterface $session) {
        if ($request->isXmlHttpRequest()) {
            $dis = $request->request->get('dis');
            $id = $request->request->get('id');
            $emailId = $session->get('email');
            $editRec = $this->allPosts;
            $editedRec = $editRec->find($id);
            $editedRec->setThumsDown($editedRec->getThumsDown() - $dis);
            $thumpsDown = $editedRec->getThumsDown();
            $loginId = $this->login->findOneBy(['email' => $emailId])->getId();
            $likeDislike = $this->likeDislike->findOneBy(['likeDislike' => $loginId, 'postDel' => $id]);
            $likeDislike->setThDown("black");
            $likeDislike->setThUp("black");
            $likeDislike->setPostDel($this->allPosts->find($id));           
            $this->em->flush();
            return new Response($thumpsDown);
        }
        return new Response('Failed');
    }

    /**
     * This route like the post.
     * 
     * @param object $session
     *   Session object store session variable.
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a count of like.
     */
    #[Route('/like', name: 'like')]
    public function like(Request $request, SessionInterface $session) {
        if ($request->isXmlHttpRequest()) {
            $dis = $request->request->get('dis');
            $id = $request->request->get('id');
            $emailId = $session->get('email');
            $editRec = $this->allPosts;
            $editedRec = $editRec->find($id);
            $editedRec->setThumsUp($editedRec->getThumsUp() + $dis);
            $thumpsDown = $editedRec->getThumsUp();
            $loginId = $this->login->findOneBy(['email' => $emailId])->getId();
            $likeDislikecheck = $this->likeDislike->findOneBy(['likeDislike' => $loginId, 'postDel' => $id]);
            if ($likeDislikecheck) {
                $likeDislikecheck->setThDown("black");
                $likeDislikecheck->setThUp("blue");
            }
            else {
                $this->likeDis->setThDown("black");
                $this->likeDis->setThUp("blue");
                $this->likeDis->setPostDel($this->allPosts->find($id));
                $this->likeDis->setLikeDislike($this->login->findOneBy(['email' => $emailId]));
                $this->em->persist($this->likeDis);
            }           
            $this->em->flush();
            return new Response($thumpsDown);
        }
        return new Response('Failed');
    }

    /**
     * This route remove like from the post.
     * 
     * @param object $session
     *   Session object store session variable.
     * @param object $request
     *   Request object handles parameter from query parameter.
     *  
     * @return Response
     *   Returns a count of like after like.
     */
    #[Route('/likeRemove', name: 'likeRemove')]
    public function likeRemove(Request $request, SessionInterface $session) {
        if ($request->isXmlHttpRequest()) {
            $dis = $request->request->get('dis');
            $id = $request->request->get('id');
            $emailId = $session->get('email');
            $editRec = $this->allPosts;
            $editedRec = $editRec->find($id);
            $editedRec->setThumsUp($editedRec->getThumsUp() - $dis);
            $thumpsDown = $editedRec->getThumsUp();
            $loginId = $this->login->findOneBy(['email' => $emailId])->getId();
            $likeDislike = $this->likeDislike->findOneBy(['likeDislike' => $loginId, 'postDel' => $id]);
            $likeDislike->setThDown("black");
            $likeDislike->setThUp("black");
            $likeDislike->setPostDel($this->allPosts->find($id));           
            $this->em->flush();
            return new Response($thumpsDown);
        }
        return new Response('Failed');
    }

    #[Route('/editProfile', name: 'editProfile')]
    public function editProfile(Request $request, SessionInterface $session) {
        $emailId = $session->get('email');
        $check = $this->login->findOneBy(['email' => $emailId]);
        if ($request->isXmlHttpRequest()) {
            $firstName = $request->request->get('fname');
            $lastName = $request->request->get('lname');
            $gender = $request->request->get('gender');
            $image = $request->files->get('image');
            $about = $request->request->get('abotYou');
            if ($image) {
                $imageLocation = new DisplayImg($image, $emailId, $gender);
                $image = $imageLocation->checkingImg();
                $check->setImg($image);
            }
            $check->setFirstName($firstName);
            $check->setLastName($lastName);
            $check->setGender($gender);
            $check->setAbout($about);
            $this->em->persist($check);
            $this->em->flush();
            return new Response('done');
        }
        return $this->render('main/editProfile.html.twig', ['firstName' => $check->getFirstName(), 'lastName' => $check->getLastName(), 'gender' => $check->getGender(), 'about' => $check->getAbout(), 'image' => $check->getImg()]);
    }

    #[Route('/sendFriendRequest', name: 'sendFriendRequest')]
    public function sendFriendRequest(Request $request, SessionInterface $session, Pusher $pusher) {
        $emailId = $session->get('email');
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $this->friendSend->setRequestStatus(0);
            $this->friendSend->setFromRequest($this->login->findOneBy(['email' => $emailId]));
            $this->friendSend->setToRequest($this->login->findOneBy(['id' => $id]));
            $this->em->persist($this->friendSend);
            $this->em->flush();
            $total = $this->friendReq->count(['requestStatus' => 0]);
            $pusher->trigger('ChirpChat-development', 'my-noti', $total);
            return new Response('done');
        }
        return new Response('failed');
    }

    #[Route('/cancleFriendRequest', name: 'cancleFriendRequest')]
    public function cancleFriendRequest(Request $request, SessionInterface $session, Pusher $pusher) {
        $emailId = $session->get('email');
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $fromRequest = $this->login->findOneBy(['email' => $emailId])->getId();
            $deleteRequest = $this->friendReq->findOneBy(['fromRequest' => $fromRequest, 'toRequest' => $id]);
            $this->em->remove($deleteRequest);
            $this->em->flush();
            $total = $this->friendReq->count(['requestStatus' => 0]);
            $pusher->trigger('ChirpChat-development', 'my-noti', $total);
            return new Response('done');
        }
        return new Response('failed');
    }

    #[Route('/notifications', name: 'notifications')]
    public function notifications(SessionInterface $session) {
        $emailId = $session->get('email');
        $fromRequest = $this->login->findOneBy(['email' => $emailId])->getId();
        $notificationFriendRequest = $this->friendReq->findBy(['toRequest' => $fromRequest, 'requestStatus' => 0]);
        if ($notificationFriendRequest) {
            $allNoti = $this->user->getFriendRequestNoti($notificationFriendRequest);
            return new JsonResponse(['noti' => $allNoti]);
        }
        return new Response('noNotification');
    }

    #[Route('/liveNotifications', name: 'liveNotifications')]
    public function liveNotifications(SessionInterface $session) {
        $emailId = $session->get('email');
        $toRequest = $this->login->findOneBy(['email' => $emailId])->getId();
        $notificationCount = $this->friendReq->count(['toRequest' => $toRequest, 'requestStatus' => 0]);
        return new Response($notificationCount);
    }

    #[Route('/reject', name: 'reject')]
    public function reject(Request $request, Pusher $pusher, SessionInterface $session) {
        if ($request->isXmlHttpRequest()) {
            $emailId = $session->get('email');
            $id = $request->request->get('id');
            $rejectReq = $this->friendReq->find($id);
            $this->em->remove($rejectReq);
            $this->em->flush();
            $total = $this->friendReq->count(['requestStatus' => 0]);
            $pusher->trigger('ChirpChat-development', 'my-noti', $total);
            return new Response('done');
        }
        return new Response('failed');
    }

    #[Route('/accept', name: 'accept')]
    public function accept(Request $request, SessionInterface $session, Pusher $pusher) {
        $emailId = $session->get('email');
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $rejectReq = $this->friendReq->find($id);
            $rejectReq->setRequestStatus(1);
            $friends = new FriendList();
            $friends->setUser($this->login->findOneBy(['email' => $emailId]));
            $friends->setFriends($this->friendReq->find($id)->getFromRequest());
            $this->em->persist($friends);
            $this->em->flush();
            $total = $this->friendReq->count(['requestStatus' => 0]);
            $pusher->trigger('ChirpChat-development', 'my-noti', $total);
            return new Response('done');
        }
        return new Response('failed');
    }

    #[Route('/searchFriend', name: 'searchFriend')]
    public function searchFriend(SessionInterface $session)
    {
        $emailId = $session->get('email');
        $userOnline = $this->login;
        $id = $userOnline->findOneBy(['email' => $emailId])->getId();
        $users = $userOnline->getOnlineUsers($emailId);
        $onlineUser = $this->user->getSearchList($users);
        $arr = [];
        forEach($onlineUser as $user) {
            $sendRequest = $this->friendReq->findOneBy(['fromRequest' => $id, 'toRequest' => $user['id'], 'requestStatus' => 0]);
            if ($sendRequest) {
                $user['requestStatus'] = 'send';
            }

            $receivedRequest = $this->friendReq->findOneBy(['toRequest' => $id, 'fromRequest' => $user['id'], 'requestStatus' => 0]);
            if ($receivedRequest) {
                $user['requestStatus'] = 'received';
                $user['requestId'] = $receivedRequest->getId();
            }

            $acceptedRequest = $this->friendReq->findOneBy(['fromRequest' => $id,'toRequest' => $user['id'], 'requestStatus' => 1]);
            if ($acceptedRequest) {
                $user['requestStatus'] = 'accepted';
            }

            $acceptedRequest = $this->friendReq->findOneBy(['toRequest' => $id, 'fromRequest' => $user['id'], 'requestStatus' => 1]);
            if ($acceptedRequest) {
                $user['requestStatus'] = 'accepted';
            }
            array_push($arr, $user);
        }
        return new JsonResponse(['userOnline' => $arr]);
    }

    #[Route('/chatsWindow', name: 'chatsWindow')]
    public function chatWindow() {
        return $this->render(view:'main/chatsWindow.html.twig');
    }

    #[Route('/friends', name: 'friends')]
    public function friends(SessionInterface $session)
    {
        $emailId = $session->get('email');
        $userOnline = $this->login;
        $id = $userOnline->findOneBy(['email' => $emailId])->getId();
        $users = $this->friendList->getFriendList($id);

        $onlineFriends = $this->user->getOnlineFriends($users, $emailId);
        return new JsonResponse(['userOnline' => $onlineFriends]);
    }
}
