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
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Versio\Version\Version;
use Versio\Version\VersionManager;

class VersioMarkingStore implements MarkingStoreInterface
{

    protected static $TYPES = [
        'ALPHA',
        'BETA',
        'RC',
        'RTM',
    ];

    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * VersioMarkingStore constructor.
     * @param VersionManager $versionManager
     */
    public function __construct(VersionManager $versionManager)
    {
        $this->versionManager = $versionManager;
    }

    /**
     * @param Version $subject
     * @return Marking
     * @throws ErrorException
     */
    public function getMarking($subject): Marking
    {
        if (!($subject instanceof Version)) {
            throw new ErrorException('Unsupported subject');
        }

        if ($subject->getPatch() >= 1) {
            return new Marking(['RELEASE' => 1]);
        }

        foreach (self::$TYPES as $type) {
            if ($this->versionManager->isType($subject, $type)) {
                return new Marking([$type => 1]);
            }

            if ($this->versionManager->isTypeDev($subject, $type)) {
                return new Marking([$type => 1]);
            }
        }

        if ($this->versionManager->isDev($subject)) {
            return new Marking(['MASTER' => 1]);
        }

        throw new ErrorException('Unsupported version marking "' . $subject->format() . '".');
    }

    /**
     * @param Version $subject
     * @param Marking $marking
     * @throws ErrorException
     */
    public function setMarking($subject, Marking $marking): void
    {
        if (!($subject instanceof Version)) {
            throw new ErrorException('Unsupported subject');
        }

        if ($marking->has('ALPHA')) {
            $this->versionManager->setType($subject, 'ALPHA');
        } else if ($marking->has('BETA')) {
            $this->versionManager->setType($subject, 'BETA');
        } else if ($marking->has('RC')) {
            $this->versionManager->setType($subject, 'RC');
        } else if ($marking->has('RTM')) {
            $this->versionManager->setType($subject, 'RTM');
        } else if ($marking->has('RELEASE')) {
            $this->versionManager->unsetType($subject);
        }
    }
}