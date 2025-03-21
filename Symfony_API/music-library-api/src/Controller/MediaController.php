<?php
// src/Controller/MediaController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    #[Route('/media-list', name: 'media_list')]
    public function list(): JsonResponse
    {
        $mediaDir = $this->getParameter('media_directory');
        $files = scandir($mediaDir);
        $images = [];
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                $images[] = $file;
            }
        }
        return new JsonResponse($images);
    }
}
