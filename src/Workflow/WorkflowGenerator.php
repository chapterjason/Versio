<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Workflow;

use ErrorException;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

class WorkflowGenerator
{

    /**
     * @var VersioMarkingStore
     */
    private $store;

    /**
     * WorkflowGenerator constructor.
     * @param VersioMarkingStore $store
     */
    public function __construct(VersioMarkingStore $store)
    {
        $this->store = $store;
    }

    /**
     * @param array $configuration
     * @return Workflow
     * @throws ErrorException
     */
    public function generate(array $configuration): Workflow
    {
        $this->validateConfiguration($configuration);

        $places = $configuration['places'];
        $transitions = $configuration['transitions'];

        $definitionBuilder = new DefinitionBuilder();

        $definitionBuilder->addPlaces(array_merge($places, ['MASTER', 'RELEASE']));

        foreach ($transitions as $start => $possibleTransitions) {
            foreach ($possibleTransitions as $transition) {
                $definitionBuilder->addTransition(new Transition($start . '_' . $transition, $start, $transition));
            }
        }

        $definition = $definitionBuilder->build();

        return new Workflow($definition, $this->store, null, 'VersionWorkflow');
    }

    /**
     * @param array $configuration
     * @throws ErrorException
     */
    protected function validateConfiguration(array $configuration): void
    {
        $places = $configuration['places'];
        $transitions = $configuration['transitions'];

        $validStarts = array_merge($places, ['MASTER']);
        $validTransitions = array_merge($places, ['RELEASE']);

        foreach ($transitions as $start => $possibleTransitions) {
            if (!in_array($start, $validStarts, true)) {
                // @todo
                throw new ErrorException('Place "' . $start . '" is not defined for usage as a start transition.');
            }

            foreach ($possibleTransitions as $transition) {
                if (!in_array($transition, $validTransitions, true)) {
                    // @todo
                    throw new ErrorException('Place "' . $transition . '" is not defined for usage as a transition.');
                }
            }
        }

        // @todo validate that all transition path ends in release
    }

}