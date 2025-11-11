<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/schedules')]
class ScheduleController extends AbstractController
{
    public function __construct(
        private ScheduleRepository $scheduleRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'schedules_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $schedules = $this->scheduleRepository->findAll();

        return $this->json([
            'data' => $schedules,
            'total' => \count($schedules),
        ], Response::HTTP_OK, [], ['groups' => ['schedule:read']]);
    }

    /**
     * Handle errors
     */
    private function handleValidationErrors($errors): ?JsonResponse
    {
        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json([
                'errors' => $errorsArray,
            ], Response::HTTP_BAD_REQUEST);
        }

        return null;
    }

    #[Route('', name: 'schedules_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $schedule = new Schedule();
        $schedule->setDay($data['day'] ?? '');
        $schedule->setStartTime(new \DateTime($data['startTime'] ?? 'now'));
        $schedule->setEndTime(new \DateTime($data['endTime'] ?? 'now'));

        $errors = $this->validator->validate($schedule);
        if ($errorResponse = $this->handleValidationErrors($errors)) {
            return $errorResponse;
        }

        $this->entityManager->persist($schedule);
        $this->entityManager->flush();

        return $this->json([
            'data' => $schedule,
        ], Response::HTTP_CREATED, [], ['groups' => ['schedule:read']]);
    }

    #[Route('/{id}', name: 'schedules_update', methods: ['PUT'])]
    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['day'])) {
            $schedule->setDay($data['day']);
        }
        if (isset($data['startTime'])) {
            $schedule->setStartTime(new \DateTime($data['startTime']));
        }
        if (isset($data['endTime'])) {
            $schedule->setEndTime(new \DateTime($data['endTime']));
        }

        $errors = $this->validator->validate($schedule);
        if ($errorResponse = $this->handleValidationErrors($errors)) {
            return $errorResponse;
        }

        $this->entityManager->flush();

        return $this->json([
            'data' => $schedule,
        ], Response::HTTP_OK, [], ['groups' => ['schedule:read']]);
    }

    #[Route('/{id}', name: 'schedules_delete', methods: ['DELETE'])]
    public function delete(Schedule $schedule): JsonResponse
    {
        $this->entityManager->remove($schedule);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Schedule deleted',
        ], Response::HTTP_NO_CONTENT);
    }
}
