<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class AssignmentApprovalLimit extends Constraint
{
    public string $message = 'Approving this assignment would exceed the limit of {{ limit }} for the position "{{ positionName }}". Currently {{ currentApproved }} approved.';

    public function __construct(
        public string $mode = 'strict',
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
