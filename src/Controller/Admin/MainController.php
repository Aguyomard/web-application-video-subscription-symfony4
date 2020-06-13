<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Video;
use App\Entity\User;
use App\Form\UserType;

// use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     * @param Request $request
     * @param UserPasswordEncoderInterface $password_encoder
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $password_encoder, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user,['user'=>$user]);
        $form->handleRequest($request);
        $is_invalid = null;
        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $user->setName($request->request->get('user')['name']);
            $user->setLastName($request->request->get('user')['last_name']);
            $user->setEmail($request->request->get('user')['email']);
            $password = $password_encoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            $translated = $translator->trans('Your changes were saved!');

            $this->addFlash(
                'success',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('admin_main_page');
        }
        elseif($request->isMethod('post'))
        {
            $is_invalid = 'is-invalid';
        }

        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
            'form'=>$form->createView(),
            'is_invalid' => $is_invalid
        ]);
    }

    /**
     * @Route("/delete-account", name="delete_account")
     */
    public function deleteAccount()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser());

        $em->remove($user);
        $em->flush();

        session_destroy();

        return $this->redirectToRoute('main_page');
    }

    /**
     * @Route("/cancel-plan", name="cancel_plan")
     */
    public function cancelPlan()
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());

        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \Datetime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->persist($subscription);
        $entityManager->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route({"en":"/videos","fr":"/liste-de-videos"} , name="videos")
     */
    public function videos(CategoryTreeAdminOptionList $categories)
    {

        if ($this->isGranted('ROLE_ADMIN'))
        {

            $categories->getCategoryList($categories->buildTree());
            $videos = $this->getDoctrine()->getRepository(Video::class)->findBy([],['title'=>'ASC']);

        }
        else
        {
            $categories = null;
            $videos = $this->getUser()->getLikedVideos();
        }

        return $this->render('admin/videos.html.twig',[
            'videos'=>$videos,
            'categories'=>$categories
        ]);
    }

}
