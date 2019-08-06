<?php

namespace UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/addRole", name="add_role")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'attr' => [
                    'maxlength' => '255',
                    'pattern' => '[a-zA-Z0-9]*'
                ]
            ])

            ->add('save', SubmitType::class)
            ->getForm();
        $username = $request->get('form');
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->findOneBy([
            'username' => $username
        ]);
        if(isset($user)) {
            $userManager = $this->get('fos_user.user_manager');
            $user->addRole("ROLE_MODERATOR");
            $userManager->updateUser($user);
        }

        return $this->render('UserBundle:Admin:admin.page.html.twig',
            [
                'form' =>$form->createView()
            ]);
    }


}
