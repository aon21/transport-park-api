<?php

namespace App\Controller;

use App\Dto\Response\OrderResource;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders', name: 'api_orders_')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $active = $request->query->get('active');
        
        if ($active === 'true') {
            $orders = $this->orderService->findActive();
        } else {
            $orders = $this->orderService->findAll($status);
        }
        
        return $this->json(OrderResource::collection($orders));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $order = $this->orderService->findById($id);
        if (!$order) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(OrderResource::fromEntity($order));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $this->orderService->create($data);

        return $this->json(OrderResource::fromEntity($order), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $order = $this->orderService->findById($id);
        if (!$order) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $order = $this->orderService->update($order, $data);

        return $this->json(OrderResource::fromEntity($order));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $order = $this->orderService->findById($id);
        if (!$order) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $this->orderService->delete($order);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

