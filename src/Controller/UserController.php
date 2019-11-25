<?php
    namespace  App\Controller;
    use App\Entity\User;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;


    class UserController extends AbstractController
    {
        // Show Single User Method
        /**
         * @Route("users/user/show/{id}", name="user_show")
         */
        public function show($id){
            // get user data from database
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($id);

            // return view user info template
            return $this->render("/users/view.html.twig", array(
                "user" => $user
            ));
        }

        // Delete Single User Method
        /**
         * @Route("users/user/delete/{id}", name="user_delete", methods={"DELETE"})
         */
        public function delete(Request $request, $id){
            // get user data from database
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            //go to homepage if success
            return $this->redirectToRoute("user_list");
        }

        // update / Edit  Single User Method
        /**
         * @Route("users/user/update/{id}", name="update_user", methods={"GET","POST"})
         */

        public function update(Request $request, $id){
            // get user data from database
                        $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($id);

            // create registration form
            $form = $this->createFormBuilder($user)
                ->add('username', TextType::class, array(
                    "attr"=>array("class" => "form-control")))
                ->add('email',TextType::class, array(
                    "attr" => array('class' => "form-control")))
                ->add('password', PasswordType::class, array(
                    'required'=> false,
                    "attr" => array("class" => "form-control")))
                ->add('update', SubmitType::class, array(
                    "label" => "Update User",
                    "attr" => array(
                        'class' => "btn btn-warning rounded mt-3",
                    )))->getForm();

            //handle the form data
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                //return to homepage after successful submitting
                return $this->redirectToRoute("user_list");
            }
            // render the update / edit  user template
            return $this->render('/users/update.html.twig', array(
                'form' => $form->createView()
            ));
        }

        // Create new User Method
        /**
         * @Route("users/user/create", name="new_user", methods={"GET","POST"})
         */

        public function create(Request $request){

            // create new user object
            $user = new User();

            // create registration form
            $form = $this->createFormBuilder($user)
                ->add('username', TextType::class, array(
                    "attr"=>array("class" => "form-control")))
                ->add('email',TextType::class, array(
                    "attr" => array('class' => "form-control")))
                ->add('password', PasswordType::class, array(
                    "attr" => array("class" => "form-control")))
                ->add('Add', SubmitType::class, array(
                    "label" => "Add New",
                    "attr" => array(
                        'class' => "btn btn-primary rounded mt-3",
                    )))->getForm();

            // handle form data
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                //redirect to homepage when success
                 return $this->redirectToRoute("user_list");
            }
            // render the new user template
            return $this->render('/users/new.html.twig', array(
                'form' => $form->createView()
            ));
        }

        // List all user in home page
        /**
         * @Route("/users", name="user_list")
         */

        public function index() {
            // get all user data from database
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findAll();

            // render the users template
            return $this->render('/users/index.html.twig',array(
                'users' => $users
            ));
        }

        // Login method
        /**
         * @Route("/", name="user_login", methods={"POST", "GET"})
         */

        public function login(Request $request) {
            //Get Users from Database
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findAll();

            // create new user object
            $loginUser = new User();

            // create registration form
            $form = $this->createFormBuilder($loginUser)
                ->add('email',TextType::class, array(
                    "attr" => array('class' => "form-control")))
                ->add('password', PasswordType::class, array(
                    "attr" => array("class" => "form-control")))
                ->add('login', SubmitType::class, array(
                    "label" => "login",
                    "attr" => array(
                        'class' => "btn btn-primary rounded mt-3",
                    )))->getForm();

            // Handles the data from form
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $loginUser = $form->getData();

                // looping through existing user to check the one is logging
                foreach ($users as $user){
                    if($user->getEmail() == $loginUser->getEmail()
                        && $user->getPassword() == $loginUser->getPassword())
                    {
                        // if success go to home page
                        return $this->redirectToRoute("user_list");
                    } else {
                        // if failed stay in same page
                        return $this->redirectToRoute("user_login");
                    }
                }
            }

            // render the login template
            return $this->render('/users/login.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }