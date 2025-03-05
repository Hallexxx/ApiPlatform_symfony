<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SongRepository;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    private SongRepository $songRepository;
    private AlbumRepository $albumRepository;
    private ArtistRepository $artistRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SongRepository $songRepository,
        AlbumRepository $albumRepository,
        ArtistRepository $artistRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->songRepository   = $songRepository;
        $this->albumRepository  = $albumRepository;
        $this->artistRepository = $artistRepository;
        $this->entityManager    = $entityManager;
    }

    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // Récupérer les créations de l'utilisateur via le champ "createdBy"
        $songs   = $this->songRepository->findBy(['createdBy' => $user]);
        $albums  = $this->albumRepository->findBy(['createdBy' => $user]);
        $artists = $this->artistRepository->findBy(['createdBy' => $user]);

        return $this->render('profile/profile.html.twig', [
            'user'    => $user,
            'songs'   => $songs,
            'albums'  => $albums,
            'artists' => $artists,
        ]);
    }

    #[Route('/profile/update', name: 'profile_update', methods: ['POST'])]
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $username = $request->request->get('username');
        $email = $request->request->get('email');

        if ($username) {
            $user->setUsername($username);
        }
        if ($email) {
            $user->setEmail($email);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success'  => true,
            'username' => $user->getUsername(),
            'email'    => $user->getEmail()
        ]);
    }
}
