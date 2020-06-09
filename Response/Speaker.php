<?php

namespace Lens\Bundle\ReadspeakerBundle\Response;

class Speaker
{
    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    const GENDER_TYPES = [
        GENDER_MALE,
        GENDER_FEMALE,
    ];

    private $language;
    private $voice;
    private $gender;

    public static function fromApiResponse(array $data)
    {
        return (new self())
            ->setLanguage($data['language'])
            ->setVoice($data['voice'])
            ->setGender($data['gender']);
    }

    private function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    private function setVoice(string $voice): self
    {
        $this->voice = $voice;

        return $this;
    }

    public function getVoice(): string
    {
        return $this->voice;
    }

    private function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }
}
