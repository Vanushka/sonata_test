<?php

namespace App\Admin;

use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

final class ProductAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $image = $this->getSubject();

        $fileFormOptions = ['required' => false];
        if ($image && ($webPath = $image->getImage())) {
            // get the request so the full path to the image can be set
            $request = $this->getRequest();
            $fullPath = $request->getBasePath().'/'.$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFormOptions['help'] = '<img width="130" width="130" src="'.Product::GET_PATH_TO_IMAGE_PRODUCT.$fullPath.'" class="admin-preview"/>';
            $fileFormOptions['help_html'] = true;
        }
        
        $formMapper
            ->add('id', TextType::class, array(
                'disabled' => true
            ))
            ->add('name', TextType::class)
            ->add('slug', TextType::class, array(
                'disabled' => true
            ))
            ->add('price', NumberType::class)
            ->add('file', FileType::class, $fileFormOptions)
            ->add('active');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('price')
            ->add('active');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->addIdentifier('slug')
            ->addIdentifier('price')
            ->add('image', TextType::class, array(
                'template' => 'product/picture.html.twig'
            ))
            ->add('active')
            ->add('created_at')
            ->add('updated_at')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ])
            ->add('product', null, array(
                'query_builder' => function(EntityRepository $er) {
                    dd($er);
                     return $er->createQueryBuilder('qb')
                               ->leftjoin('qb.products', 'p')
                               ->where('p.active = :act')
                               ->setParameter('act', true);
                 }
            ));
    }

    protected function configureShowFields(ShowMapper $showMaper): void
    {
        $showMaper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('price')
            ->add('image', NULL, array(
                    'template' => 'product/show.image.html.twig'
                )
            )
            ->add('active')
            ->add('created_at')
            ->add('updated_at');
    }

    public function prePersist($user)
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($user->getName());
        $user->setSlug($slug);
        $this->manageFileUpload($user);
    }

    public function preUpdate($user)
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($user->getName());
        $user->setSlug($slug);
        $this->manageFileUpload($user);
    }

    private function manageFileUpload(object $user): void
    {
        if ($user->getFile()) {
            $user->refreshUpdated();
            $user->upload();
        }
    }
    
}