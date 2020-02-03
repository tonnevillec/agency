<?php
namespace App\Listener;

use App\Entity\Property;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber Implements EventSubscriber
{

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $helper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $helper)
    {
        $this->cacheManager = $cacheManager;
        $this->helper = $helper;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Property) {
            if ($args->getEntity()->getImageFile() instanceof UploadedFile) {
                $this->removeImgCache($args->getEntity());
            }
        }
        return;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->getEntity() instanceof Property) {
            if ($args->getEntity()->getImageFile() instanceof UploadedFile) {
                $this->removeImgCache($args->getEntity());
            }
        }
        return;
    }

    private function removeImgCache($entity) {
        $this->cacheManager->remove($this->helper->asset($entity, 'imageFile'));
    }
}