<?php

namespace App\Tests\Unit;

use App\Dto\Request\OrderCreateRequest;
use App\Dto\Request\OrderUpdateRequest;
use App\Validator\Constraints\AtLeastOneOrderAsset;
use App\Validator\Constraints\AtLeastOneOrderAssetValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class AtLeastOneOrderAssetValidatorTest extends TestCase
{
    private AtLeastOneOrderAssetValidator $validator;
    private ExecutionContextInterface $context;

    protected function setUp(): void
    {
        $this->validator = new AtLeastOneOrderAssetValidator();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    public function testValidateWithTruck(): void
    {
        $request = new OrderCreateRequest();
        $request->truckId = 'truck-uuid';

        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate($request, new AtLeastOneOrderAsset());
    }

    public function testValidateWithTrailer(): void
    {
        $request = new OrderCreateRequest();
        $request->trailerId = 'trailer-uuid';

        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate($request, new AtLeastOneOrderAsset());
    }

    public function testValidateWithFleetSet(): void
    {
        $request = new OrderCreateRequest();
        $request->fleetSetId = 'fleet-uuid';

        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate($request, new AtLeastOneOrderAsset());
    }

    public function testValidateWithMultipleAssets(): void
    {
        $request = new OrderCreateRequest();
        $request->truckId = 'truck-uuid';
        $request->trailerId = 'trailer-uuid';
        $request->fleetSetId = 'fleet-uuid';

        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate($request, new AtLeastOneOrderAsset());
    }

    public function testValidateWithNoAssets(): void
    {
        $request = new OrderCreateRequest();

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with('Order must have at least one asset assigned (truck, trailer, or fleet set)')
            ->willReturn($builder);

        $this->validator->validate($request, new AtLeastOneOrderAsset());
    }

    public function testValidateWithEmptyStrings(): void
    {
        $request = new OrderCreateRequest();
        $request->truckId = '';
        $request->trailerId = '';
        $request->fleetSetId = '';

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($builder);

        $this->validator->validate($request, new AtLeastOneOrderAsset());
    }

    public function testValidateWithNullValue(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate(null, new AtLeastOneOrderAsset());
    }

    public function testValidateWorksWithOrderUpdateRequest(): void
    {
        $request = new OrderUpdateRequest();
        $request->truckId = 'truck-uuid';

        $this->context->expects($this->never())->method('buildViolation');
        $this->validator->validate($request, new AtLeastOneOrderAsset());
    }

    public function testThrowsExceptionForInvalidConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        
        $invalidConstraint = $this->createMock(Constraint::class);
        $request = new OrderCreateRequest();
        
        $this->validator->validate($request, $invalidConstraint);
    }

    public function testConstraintMessage(): void
    {
        $constraint = new AtLeastOneOrderAsset();
        $this->assertEquals(
            'Order must have at least one asset assigned (truck, trailer, or fleet set)',
            $constraint->message
        );
    }

    public function testConstraintTarget(): void
    {
        $constraint = new AtLeastOneOrderAsset();
        $this->assertEquals(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
