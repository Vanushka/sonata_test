<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

final class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('id', TextType::class, array(
                'disabled' => true
            ))
            ->add('username', TextType::class)
            ->add('email', TextType::class)
            ->add('active')
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Password confirmation'),
                'required' => false
            ))
            ->add('roles', ChoiceType::class, array(
                'required' => true,
                'label' => 'Roles',
                'choices' => array(
                    'Admin' => 'ROLE_ADMIN',
                    'Manager' => 'ROLE_MANAGER'
                ),
                'multiple' => true,
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('active')
            ->add('roles');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('username')
            ->addIdentifier('email')
            ->add('rolesAsString', CollectionType::class,
                array(
                    'label' =>  'Roles',
                    'roles' => array(
                        'Admin' => 'ROLE_ADMIN',
                        'Manager' => 'ROLE_MANAGER'
                    ),
                ),
            )
            ->add('active')
            ->add('created_at')
            ->add('updated_at')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    protected function configureShowFields(ShowMapper $showMaper): void
    {
        $showMaper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('rolesAsString', CollectionType::class,
                array(
                    'label' =>  'Roles',
                    'roles' => array(
                        'Admin' => 'ROLE_ADMIN',
                        'Manager' => 'ROLE_MANAGER'
                    ),
                ),
            )
            ->add('created_at')
            ->add('updated_at');
    }

    public function prePersist($user) { // $object is an instance of App\Entity\User as specified in services.yaml
        // dd($user->getRoles());
        $plainPassword = $user->getPlainPassword();
        $container = $this->getConfigurationPool()->getContainer();
        $encoder = $container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);
    }

    public function preUpdate($user) { // $object is an instance of App\Entity\User as specified in services.yaml
        $plainPassword = $user->getPlainPassword();
        $container = $this->getConfigurationPool()->getContainer();
        $encoder = $container->get('security.password_encoder');
        if(!is_null($plainPassword)) {
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
        }
    }
}