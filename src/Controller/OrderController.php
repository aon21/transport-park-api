<?php

namespace App\Controller;

use App\Dto\Request\OrderCreateRequest;
use App\Dto\Request\OrderUpdateRequest;
use App\Dto\Response\OrderResource;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders', name: 'api_orders_')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderService $orderService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $orders = $this->orderRepository->findAllWithRelations();

        return $this->json(OrderResource::collection($orders));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Order $order): JsonResponse
    {
        return $this->json(OrderResource::fromEntity($order));
    }

    /**
     * @throws Exception
     */
    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] OrderCreateRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request);

        return $this->json(OrderResource::fromEntity($order), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(Order $order, #[MapRequestPayload] OrderUpdateRequest $request): JsonResponse
    {
        $order = $this->orderService->update($order, $request);

        return $this->json(OrderResource::fromEntity($order));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Order $order): JsonResponse
    {
        $this->orderService->delete($order);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

