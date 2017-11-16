<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 016 16.11.17
 * Time: 17:15
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Image;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ImageAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $fileFieldOptions = $this->getImageBlock();

        $form
            ->add('file', 'file', $fileFieldOptions);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $fileFieldOptions = $this->getImageBlock();
        $list->addIdentifier('filename', 'file', $fileFieldOptions);
    }

    private function getImageBlock()
    {
        // get the current Image instance
        $image = $this->getSubject();

        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('required' => false);
        if ($image && ($webPath = $image->getWebPath())) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request_stack')->getCurrentRequest()->getBasePath().'/'.$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" style="max-height: 200px; max-width: 200px;" />';
        }
        return $fileFieldOptions;
    }

    /**
     * @param Image $image
     */
    public function prePersist($image)
    {
        $this->manageFileUpload($image);
    }

    /**
     * @param Image $image
     */
    public function preUpdate($image)
    {
        $this->manageFileUpload($image);
    }

    /**
     * @param $image Image
     */
    private function manageFileUpload($image)
    {
        if ($image->getFile()) {
            $image->refreshUpdated();
        }
    }
}