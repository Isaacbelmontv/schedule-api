<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class TimeNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'TIME_NORMALIZER_ALREADY_CALLED';

    public function normalize($object, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        if (isset($data['startTime']) && $object->getStartTime() instanceof \DateTimeInterface) {
            $data['startTime'] = $object->getStartTime()->format('H:i');
        }

        if (isset($data['endTime']) && $object->getEndTime() instanceof \DateTimeInterface) {
            $data['endTime'] = $object->getEndTime()->format('H:i');
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof \App\Entity\Schedule;
    }
}
